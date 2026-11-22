<?php

namespace Modules\QueryMonitor\Storage;

use PDO;
use RuntimeException;

class MonitorStore
{
    /** @var PDO|null */
    protected $pdo = null;

    /** @var string */
    protected $sqlitePath;

    public function __construct(string $sqlitePath)
    {
        $this->sqlitePath = $sqlitePath;
    }

    public function pdo(): PDO
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $dir = dirname($this->sqlitePath);
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException("Unable to create QueryMonitor storage directory: {$dir}");
        }

        $pdo = new PDO('sqlite:' . $this->sqlitePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $pdo->exec('PRAGMA journal_mode = WAL;');
        $pdo->exec('PRAGMA synchronous = NORMAL;');
        $pdo->exec('PRAGMA busy_timeout = 5000;');
        $pdo->exec('PRAGMA foreign_keys = ON;');

        $this->pdo = $pdo;
        $this->ensureSchema();

        return $this->pdo;
    }

    protected function ensureSchema(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS monitor_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'admin',
                is_active INTEGER NOT NULL DEFAULT 1,
                last_login_at TEXT NULL,
                created_at TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS monitor_settings (
                key TEXT PRIMARY KEY,
                value TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS tuning_notes (
                sql_hash TEXT PRIMARY KEY,
                status TEXT NOT NULL DEFAULT 'open',
                note TEXT NOT NULL DEFAULT '',
                before_ms REAL NULL,
                after_ms REAL NULL,
                updated_at TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS query_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                request_id TEXT NOT NULL,
                connection TEXT NOT NULL,
                sql_raw TEXT NOT NULL,
                sql_normalized TEXT NOT NULL,
                sql_hash TEXT NOT NULL,
                bindings_json TEXT NULL,
                query_type TEXT NOT NULL,
                time_ms REAL NOT NULL,
                is_slow INTEGER NOT NULL DEFAULT 0,
                duplicate_in_request INTEGER NOT NULL DEFAULT 1,
                http_method TEXT NULL,
                uri TEXT NULL,
                route_name TEXT NULL,
                caller TEXT NULL,
                created_at TEXT NOT NULL
            );

            CREATE INDEX IF NOT EXISTS idx_qlogs_created_at ON query_logs(created_at);
            CREATE INDEX IF NOT EXISTS idx_qlogs_sql_hash ON query_logs(sql_hash);
            CREATE INDEX IF NOT EXISTS idx_qlogs_time_ms ON query_logs(time_ms);
            CREATE INDEX IF NOT EXISTS idx_qlogs_is_slow ON query_logs(is_slow);
            CREATE INDEX IF NOT EXISTS idx_qlogs_request_id ON query_logs(request_id);
            CREATE INDEX IF NOT EXISTS idx_qlogs_uri ON query_logs(uri);
        ");
    }

    // ------------------------------------------------------------------
    // Settings
    // ------------------------------------------------------------------

    public function getSetting(string $key, $default = null)
    {
        $stmt = $this->pdo()->prepare('SELECT value FROM monitor_settings WHERE key = :key LIMIT 1');
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();

        return $row ? $row['value'] : $default;
    }

    public function setSetting(string $key, string $value): void
    {
        $stmt = $this->pdo()->prepare('
            INSERT INTO monitor_settings (key, value) VALUES (:key, :value)
            ON CONFLICT(key) DO UPDATE SET value = excluded.value
        ');
        $stmt->execute([':key' => $key, ':value' => $value]);
    }

    public function isRecordingEnabled(): bool
    {
        return $this->getSetting('recording_enabled', '1') === '1';
    }

    public function getSlowThresholdMs(float $fallback): float
    {
        $val = $this->getSetting('slow_threshold_ms');
        return $val !== null ? (float) $val : $fallback;
    }

    // ------------------------------------------------------------------
    // Batch insert query logs
    // ------------------------------------------------------------------

    public function insertBatch(array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        $pdo = $this->pdo();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('
                INSERT INTO query_logs (
                    request_id, connection, sql_raw, sql_normalized, sql_hash,
                    bindings_json, query_type, time_ms, is_slow, duplicate_in_request,
                    http_method, uri, route_name, caller, created_at
                ) VALUES (
                    :request_id, :connection, :sql_raw, :sql_normalized, :sql_hash,
                    :bindings_json, :query_type, :time_ms, :is_slow, :duplicate_in_request,
                    :http_method, :uri, :route_name, :caller, :created_at
                )
            ');

            foreach ($rows as $row) {
                $stmt->execute([
                    ':request_id' => $row['request_id'],
                    ':connection' => $row['connection'],
                    ':sql_raw' => $row['sql_raw'],
                    ':sql_normalized' => $row['sql_normalized'],
                    ':sql_hash' => $row['sql_hash'],
                    ':bindings_json' => $row['bindings_json'],
                    ':query_type' => $row['query_type'],
                    ':time_ms' => $row['time_ms'],
                    ':is_slow' => $row['is_slow'],
                    ':duplicate_in_request' => $row['duplicate_in_request'],
                    ':http_method' => $row['http_method'],
                    ':uri' => $row['uri'],
                    ':route_name' => $row['route_name'],
                    ':caller' => $row['caller'],
                    ':created_at' => $row['created_at'],
                ]);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    // ------------------------------------------------------------------
    // Dashboard & Analytics Queries
    // ------------------------------------------------------------------

    public function getOverviewStats(string $sinceIso): array
    {
        $stmt = $this->pdo()->prepare("
            SELECT
                COUNT(*) AS total_queries,
                COALESCE(SUM(is_slow), 0) AS slow_queries,
                COALESCE(AVG(time_ms), 0) AS avg_ms,
                COALESCE(MAX(time_ms), 0) AS max_ms,
                COALESCE(SUM(time_ms), 0) AS total_ms,
                COUNT(DISTINCT request_id) AS total_requests,
                COUNT(DISTINCT sql_hash) AS unique_patterns
            FROM query_logs
            WHERE created_at >= :since
        ");
        $stmt->execute([':since' => $sinceIso]);
        $stats = $stmt->fetch() ?: [];

        $n1Stmt = $this->pdo()->prepare("
            SELECT COUNT(*) AS n1_hotspots
            FROM (
                SELECT request_id, sql_hash
                FROM query_logs
                WHERE created_at >= :since AND duplicate_in_request >= 3
                GROUP BY request_id, sql_hash
            )
        ");
        $n1Stmt->execute([':since' => $sinceIso]);
        $stats['n1_hotspots'] = (int) ($n1Stmt->fetch()['n1_hotspots'] ?? 0);

        $stats['db_size_bytes'] = is_file($this->sqlitePath) ? filesize($this->sqlitePath) : 0;

        return $stats;
    }

    public function getTopFingerprints(string $sinceIso, string $sortBy = 'total_ms', int $limit = 20): array
    {
        $orderMap = [
            'total_ms' => 'total_ms DESC',
            'avg_ms' => 'avg_ms DESC',
            'max_ms' => 'max_ms DESC',
            'exec_count' => 'exec_count DESC',
        ];
        $orderSql = $orderMap[$sortBy] ?? 'total_ms DESC';

        $stmt = $this->pdo()->prepare("
            SELECT
                sql_hash,
                MIN(sql_normalized) AS sql_normalized,
                MIN(query_type) AS query_type,
                MIN(connection) AS connection,
                COUNT(*) AS exec_count,
                SUM(is_slow) AS slow_count,
                AVG(time_ms) AS avg_ms,
                MAX(time_ms) AS max_ms,
                SUM(time_ms) AS total_ms,
                MAX(duplicate_in_request) AS max_dup,
                MAX(created_at) AS last_seen
            FROM query_logs
            WHERE created_at >= :since
            GROUP BY sql_hash
            ORDER BY {$orderSql}
            LIMIT :limit
        ");
        $stmt->bindValue(':since', $sinceIso);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getTopRoutes(string $sinceIso, int $limit = 15): array
    {
        $stmt = $this->pdo()->prepare("
            SELECT
                COALESCE(NULLIF(uri, ''), 'CLI / Artisan') AS uri,
                MIN(http_method) AS http_method,
                COUNT(DISTINCT request_id) AS request_count,
                COUNT(*) AS total_queries,
                ROUND(CAST(COUNT(*) AS REAL) / MAX(COUNT(DISTINCT request_id), 1), 1) AS queries_per_req,
                SUM(time_ms) AS total_ms,
                ROUND(SUM(time_ms) / MAX(COUNT(DISTINCT request_id), 1), 2) AS avg_ms_per_req,
                MAX(time_ms) AS max_query_ms,
                SUM(is_slow) AS slow_queries
            FROM query_logs
            WHERE created_at >= :since
            GROUP BY COALESCE(NULLIF(uri, ''), 'CLI / Artisan')
            ORDER BY total_ms DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':since', $sinceIso);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getN1Hotspots(string $sinceIso, int $minDup = 3, int $limit = 15): array
    {
        $stmt = $this->pdo()->prepare("
            SELECT
                request_id,
                sql_hash,
                MIN(sql_normalized) AS sql_normalized,
                MIN(uri) AS uri,
                MIN(http_method) AS http_method,
                MIN(caller) AS caller,
                MAX(duplicate_in_request) AS dup_count,
                SUM(time_ms) AS total_ms,
                AVG(time_ms) AS avg_ms,
                MAX(created_at) AS created_at
            FROM query_logs
            WHERE created_at >= :since AND duplicate_in_request >= :min_dup
            GROUP BY request_id, sql_hash
            ORDER BY dup_count DESC, total_ms DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':since', $sinceIso);
        $stmt->bindValue(':min_dup', $minDup, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function searchQueryLogs(array $filters, int $page = 1, int $perPage = 50): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['since'])) {
            $where[] = 'created_at >= :since';
            $params[':since'] = $filters['since'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(sql_raw LIKE :q OR uri LIKE :q OR caller LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['query_type'])) {
            $where[] = 'query_type = :qtype';
            $params[':qtype'] = strtoupper($filters['query_type']);
        }
        if (!empty($filters['min_ms'])) {
            $where[] = 'time_ms >= :min_ms';
            $params[':min_ms'] = (float) $filters['min_ms'];
        }
        if (!empty($filters['slow_only'])) {
            $where[] = 'is_slow = 1';
        }
        if (!empty($filters['n1_only'])) {
            $where[] = 'duplicate_in_request >= 3';
        }
        if (!empty($filters['uri'])) {
            $where[] = 'uri LIKE :uri';
            $params[':uri'] = '%' . $filters['uri'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $countStmt = $this->pdo()->prepare("SELECT COUNT(*) AS cnt FROM query_logs WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int) ($countStmt->fetch()['cnt'] ?? 0);

        $offset = max(0, ($page - 1) * $perPage);
        $orderBy = ($filters['sort'] ?? 'latest') === 'slowest' ? 'time_ms DESC, id DESC' : 'id DESC';

        $stmt = $this->pdo()->prepare("
            SELECT * FROM query_logs
            WHERE {$whereSql}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function getFingerprintDetail(string $sqlHash): ?array
    {
        $stmt = $this->pdo()->prepare("
            SELECT
                sql_hash,
                MIN(sql_normalized) AS sql_normalized,
                MIN(query_type) AS query_type,
                MIN(connection) AS connection,
                COUNT(*) AS exec_count,
                SUM(is_slow) AS slow_count,
                AVG(time_ms) AS avg_ms,
                MIN(time_ms) AS min_ms,
                MAX(time_ms) AS max_ms,
                SUM(time_ms) AS total_ms,
                MAX(duplicate_in_request) AS max_dup,
                MIN(created_at) AS first_seen,
                MAX(created_at) AS last_seen
            FROM query_logs
            WHERE sql_hash = :hash
            GROUP BY sql_hash
        ");
        $stmt->execute([':hash' => $sqlHash]);
        $summary = $stmt->fetch();

        if (!$summary) {
            return null;
        }

        $samplesStmt = $this->pdo()->prepare("
            SELECT * FROM query_logs
            WHERE sql_hash = :hash
            ORDER BY time_ms DESC, id DESC
            LIMIT 20
        ");
        $samplesStmt->execute([':hash' => $sqlHash]);
        $summary['samples'] = $samplesStmt->fetchAll();

        $callersStmt = $this->pdo()->prepare("
            SELECT
                COALESCE(NULLIF(caller, ''), 'Unknown') AS caller,
                COALESCE(NULLIF(uri, ''), 'CLI') AS uri,
                COUNT(*) AS cnt,
                AVG(time_ms) AS avg_ms,
                MAX(time_ms) AS max_ms
            FROM query_logs
            WHERE sql_hash = :hash
            GROUP BY caller, uri
            ORDER BY cnt DESC
            LIMIT 10
        ");
        $callersStmt->execute([':hash' => $sqlHash]);
        $summary['callers'] = $callersStmt->fetchAll();

        return $summary;
    }

    public function getQueryById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM query_logs WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getQueriesByRequestId(string $requestId, int $limit = 100): array
    {
        $stmt = $this->pdo()->prepare('
            SELECT id, query_type, sql_raw, sql_hash, time_ms, is_slow, duplicate_in_request, caller, created_at
            FROM query_logs
            WHERE request_id = :rid
            ORDER BY id ASC
            LIMIT :limit
        ');
        $stmt->bindValue(':rid', $requestId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function toDisplayTime(?string $utcDatetime, string $format = 'Y-m-d H:i:s'): ?string
    {
        if (empty($utcDatetime)) {
            return $utcDatetime;
        }
        try {
            $tzName = (string) config('query-monitor.timezone', 'Asia/Ho_Chi_Minh');
            $dt = new \DateTimeImmutable($utcDatetime, new \DateTimeZone('UTC'));
            return $dt->setTimezone(new \DateTimeZone($tzName))->format($format);
        } catch (\Throwable $e) {
            return $utcDatetime;
        }
    }

    public static function nowDisplay(string $format = 'H:i:s'): string
    {
        $tzName = (string) config('query-monitor.timezone', 'Asia/Ho_Chi_Minh');
        return (new \DateTimeImmutable('now', new \DateTimeZone($tzName)))->format($format);
    }

    public function getTimeSeries(int $windowSeconds = 300, int $bucketSeconds = 10): array
    {
        $now = time();
        $endBucket = (int) (floor($now / $bucketSeconds) * $bucketSeconds);
        $bucketCount = max(6, (int) ceil($windowSeconds / $bucketSeconds));
        $startTs = $endBucket - (($bucketCount - 1) * $bucketSeconds);
        $sinceIso = gmdate('Y-m-d H:i:s', $startTs);
        $tz = new \DateTimeZone((string) config('query-monitor.timezone', 'Asia/Ho_Chi_Minh'));

        $stmt = $this->pdo()->prepare("
            SELECT
                (CAST(strftime('%s', created_at) AS INTEGER) / :bucket) * :bucket2 AS bucket_ts,
                COUNT(*) AS total,
                COALESCE(SUM(is_slow), 0) AS slow,
                COALESCE(AVG(time_ms), 0) AS avg_ms,
                COALESCE(MAX(time_ms), 0) AS max_ms,
                COALESCE(SUM(time_ms), 0) AS total_ms
            FROM query_logs
            WHERE created_at >= :since
            GROUP BY bucket_ts
            ORDER BY bucket_ts ASC
        ");
        $stmt->bindValue(':bucket', $bucketSeconds, PDO::PARAM_INT);
        $stmt->bindValue(':bucket2', $bucketSeconds, PDO::PARAM_INT);
        $stmt->bindValue(':since', $sinceIso);
        $stmt->execute();

        $indexed = [];
        foreach ($stmt->fetchAll() as $row) {
            $indexed[(int) $row['bucket_ts']] = $row;
        }

        $series = [];
        for ($i = 0; $i < $bucketCount; $i++) {
            $ts = $startTs + ($i * $bucketSeconds);
            $r = $indexed[$ts] ?? null;
            $label = (new \DateTimeImmutable('@' . $ts))->setTimezone($tz)->format('H:i:s');
            $series[] = [
                'ts' => $ts,
                'label' => $label,
                'total' => $r ? (int) $r['total'] : 0,
                'slow' => $r ? (int) $r['slow'] : 0,
                'avg_ms' => $r ? round((float) $r['avg_ms'], 2) : 0.0,
                'max_ms' => $r ? round((float) $r['max_ms'], 2) : 0.0,
                'total_ms' => $r ? round((float) $r['total_ms'], 2) : 0.0,
            ];
        }

        return $series;
    }

    public function getTypeBreakdown(string $sinceIso): array
    {
        $stmt = $this->pdo()->prepare("
            SELECT
                query_type,
                COUNT(*) AS cnt,
                COALESCE(SUM(time_ms), 0) AS total_ms,
                COALESCE(AVG(time_ms), 0) AS avg_ms
            FROM query_logs
            WHERE created_at >= :since
            GROUP BY query_type
            ORDER BY cnt DESC
        ");
        $stmt->execute([':since' => $sinceIso]);
        return $stmt->fetchAll();
    }

    public function getRecentQueries(int $limit = 60, array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['since'])) {
            $where[] = 'created_at >= :since';
            $params[':since'] = $filters['since'];
        }
        if (!empty($filters['slow_only'])) {
            $where[] = 'is_slow = 1';
        }
        if (!empty($filters['n1_only'])) {
            $where[] = 'duplicate_in_request >= 3';
        }
        if (!empty($filters['q'])) {
            $where[] = '(sql_raw LIKE :q OR uri LIKE :q OR caller LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $stmt = $this->pdo()->prepare("
            SELECT id, request_id, connection, sql_raw, sql_normalized, sql_hash,
                   query_type, time_ms, is_slow, duplicate_in_request,
                   http_method, uri, caller, created_at
            FROM query_logs
            WHERE {$whereSql}
            ORDER BY id DESC
            LIMIT :limit
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function purgeLogs(?int $olderThanDays = null): int
    {
        if ($olderThanDays === null || $olderThanDays <= 0) {
            $count = (int) $this->pdo()->query('SELECT COUNT(*) FROM query_logs')->fetchColumn();
            $this->pdo()->exec('DELETE FROM query_logs; VACUUM;');
            return $count;
        }

        $cutoff = gmdate('Y-m-d H:i:s', time() - ($olderThanDays * 86400));
        $stmt = $this->pdo()->prepare('DELETE FROM query_logs WHERE created_at < :cutoff');
        $stmt->execute([':cutoff' => $cutoff]);
        return $stmt->rowCount();
    }

    // ------------------------------------------------------------------
    // Independent Module Users
    // ------------------------------------------------------------------

    public function countUsers(): int
    {
        return (int) $this->pdo()->query('SELECT COUNT(*) FROM monitor_users')->fetchColumn();
    }

    public function listUsers(): array
    {
        return $this->pdo()->query('
            SELECT id, name, email, role, is_active, last_login_at, created_at
            FROM monitor_users
            ORDER BY id ASC
        ')->fetchAll();
    }

    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM monitor_users WHERE lower(email) = lower(:email) LIMIT 1');
        $stmt->execute([':email' => trim($email)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findUserById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM monitor_users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function createUser(string $name, string $email, string $password, string $role = 'admin'): int
    {
        $stmt = $this->pdo()->prepare('
            INSERT INTO monitor_users (name, email, password_hash, role, is_active, created_at)
            VALUES (:name, :email, :password_hash, :role, 1, :created_at)
        ');
        $stmt->execute([
            ':name' => trim($name),
            ':email' => strtolower(trim($email)),
            ':password_hash' => password_hash($password, PASSWORD_BCRYPT),
            ':role' => in_array($role, ['admin', 'viewer'], true) ? $role : 'viewer',
            ':created_at' => gmdate('Y-m-d H:i:s'),
        ]);

        return (int) $this->pdo()->lastInsertId();
    }

    public function updateUserPassword(int $id, string $newPassword): void
    {
        $stmt = $this->pdo()->prepare('UPDATE monitor_users SET password_hash = :hash WHERE id = :id');
        $stmt->execute([
            ':hash' => password_hash($newPassword, PASSWORD_BCRYPT),
            ':id' => $id,
        ]);
    }

    public function setUserActive(int $id, bool $active): void
    {
        $stmt = $this->pdo()->prepare('UPDATE monitor_users SET is_active = :active WHERE id = :id');
        $stmt->execute([':active' => $active ? 1 : 0, ':id' => $id]);
    }

    public function deleteUser(int $id): void
    {
        $stmt = $this->pdo()->prepare('DELETE FROM monitor_users WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function touchUserLogin(int $id): void
    {
        $stmt = $this->pdo()->prepare('UPDATE monitor_users SET last_login_at = :now WHERE id = :id');
        $stmt->execute([':now' => gmdate('Y-m-d H:i:s'), ':id' => $id]);
    }

    // ------------------------------------------------------------------
    // SQL Tuning Workbench Queries & Notes
    // ------------------------------------------------------------------

    public function getPageTuningSummary(string $sinceIso, int $limit = 25): array
    {
        $stmt = $this->pdo()->prepare("
            SELECT
                COALESCE(NULLIF(uri, ''), 'CLI / Artisan') AS uri,
                MIN(http_method) AS http_method,
                COUNT(DISTINCT request_id) AS request_count,
                COUNT(*) AS total_queries,
                ROUND(CAST(COUNT(*) AS REAL) / MAX(COUNT(DISTINCT request_id), 1), 1) AS queries_per_req,
                SUM(time_ms) AS total_ms,
                ROUND(SUM(time_ms) / MAX(COUNT(DISTINCT request_id), 1), 2) AS avg_ms_per_req,
                MAX(time_ms) AS max_query_ms,
                SUM(is_slow) AS slow_queries,
                COUNT(DISTINCT CASE WHEN duplicate_in_request >= 3 THEN sql_hash END) AS n1_patterns
            FROM query_logs
            WHERE created_at >= :since
            GROUP BY COALESCE(NULLIF(uri, ''), 'CLI / Artisan')
            ORDER BY avg_ms_per_req DESC, total_ms DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':since', $sinceIso);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getTuningQueue(string $sinceIso, int $limit = 60): array
    {
        $stmt = $this->pdo()->prepare("
            SELECT
                q.sql_hash,
                MIN(q.sql_normalized) AS sql_normalized,
                MAX(q.sql_raw) AS sql_raw,
                MAX(q.bindings_json) AS sample_bindings,
                MIN(q.query_type) AS query_type,
                MIN(q.connection) AS connection,
                MIN(q.uri) AS sample_uri,
                MIN(q.caller) AS sample_caller,
                MAX(q.id) AS sample_id,
                COUNT(*) AS exec_count,
                SUM(q.is_slow) AS slow_count,
                AVG(q.time_ms) AS avg_ms,
                MAX(q.time_ms) AS max_ms,
                SUM(q.time_ms) AS total_ms,
                MAX(q.duplicate_in_request) AS max_dup,
                t.status AS tuning_status,
                t.note AS tuning_note,
                t.before_ms AS note_before_ms,
                t.after_ms AS note_after_ms,
                t.updated_at AS note_updated_at
            FROM query_logs q
            LEFT JOIN tuning_notes t ON t.sql_hash = q.sql_hash
            WHERE q.created_at >= :since
            GROUP BY q.sql_hash
            ORDER BY total_ms DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':since', $sinceIso);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getRiverFlowRows(string $sinceIso, int $limit = 120): array
    {
        $stmt = $this->pdo()->prepare("
            SELECT
                COALESCE(NULLIF(uri, ''), 'CLI / Artisan') AS uri,
                MIN(http_method) AS http_method,
                sql_hash,
                MIN(sql_normalized) AS sql_normalized,
                MAX(sql_raw) AS sql_raw,
                MAX(bindings_json) AS sample_bindings,
                MIN(query_type) AS query_type,
                MIN(caller) AS sample_caller,
                MAX(id) AS sample_id,
                COUNT(*) AS exec_count,
                SUM(is_slow) AS slow_count,
                AVG(time_ms) AS avg_ms,
                MAX(time_ms) AS max_ms,
                SUM(time_ms) AS total_ms,
                MAX(duplicate_in_request) AS max_dup
            FROM query_logs
            WHERE created_at >= :since
            GROUP BY COALESCE(NULLIF(uri, ''), 'CLI / Artisan'), sql_hash
            ORDER BY total_ms DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':since', $sinceIso);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getPageFingerprints(string $uri, string $sinceIso): array
    {
        $stmt = $this->pdo()->prepare("
            SELECT
                q.sql_hash,
                MIN(q.sql_normalized) AS sql_normalized,
                MAX(q.sql_raw) AS sql_raw,
                MAX(q.bindings_json) AS sample_bindings,
                MIN(q.query_type) AS query_type,
                MIN(q.connection) AS connection,
                MIN(q.caller) AS sample_caller,
                MAX(q.id) AS sample_id,
                COUNT(*) AS exec_count,
                SUM(q.is_slow) AS slow_count,
                AVG(q.time_ms) AS avg_ms,
                MAX(q.time_ms) AS max_ms,
                SUM(q.time_ms) AS total_ms,
                MAX(q.duplicate_in_request) AS max_dup,
                t.status AS tuning_status,
                t.note AS tuning_note
            FROM query_logs q
            LEFT JOIN tuning_notes t ON t.sql_hash = q.sql_hash
            WHERE q.created_at >= :since
              AND COALESCE(NULLIF(q.uri, ''), 'CLI / Artisan') = :uri
            GROUP BY q.sql_hash
            ORDER BY total_ms DESC
            LIMIT 80
        ");
        $stmt->execute([':since' => $sinceIso, ':uri' => $uri]);
        return $stmt->fetchAll();
    }

    public function saveTuningNote(string $sqlHash, string $status, string $note, ?float $beforeMs, ?float $afterMs): void
    {
        $stmt = $this->pdo()->prepare("
            INSERT INTO tuning_notes (sql_hash, status, note, before_ms, after_ms, updated_at)
            VALUES (:hash, :status, :note, :before_ms, :after_ms, :updated_at)
            ON CONFLICT(sql_hash) DO UPDATE SET
                status = excluded.status,
                note = excluded.note,
                before_ms = excluded.before_ms,
                after_ms = excluded.after_ms,
                updated_at = excluded.updated_at
        ");
        $stmt->execute([
            ':hash' => $sqlHash,
            ':status' => $status,
            ':note' => $note,
            ':before_ms' => $beforeMs,
            ':after_ms' => $afterMs,
            ':updated_at' => gmdate('Y-m-d H:i:s'),
        ]);
    }
}
