<?php

namespace Modules\QueryMonitor\Analyzer;

use Illuminate\Support\Facades\DB;

class TuningAnalyzer
{
    /** @var ExplainAnalyzer */
    protected $explainAnalyzer;

    public function __construct(ExplainAnalyzer $explainAnalyzer)
    {
        $this->explainAnalyzer = $explainAnalyzer;
    }

    /**
     * Static SQL anti-pattern & performance issue detector with Vietnamese tuning advice.
     */
    public function analyzeQuery(array $fp, float $slowThresholdMs = 100.0): array
    {
        $sql = (string) ($fp['sql_normalized'] ?? $fp['sql_raw'] ?? '');
        $avgMs = (float) ($fp['avg_ms'] ?? 0);
        $maxMs = (float) ($fp['max_ms'] ?? 0);
        $totalMs = (float) ($fp['total_ms'] ?? 0);
        $maxDup = (int) ($fp['max_dup'] ?? 1);
        $execCount = (int) ($fp['exec_count'] ?? 1);

        $issues = [];
        $score = 0;

        // 1. N+1 / Repeated query in single request
        if ($maxDup >= 3) {
            $sev = $maxDup >= 15 ? 'critical' : 'high';
            $score += ($sev === 'critical' ? 40 : 25);
            $issues[] = [
                'code' => 'N_PLUS_ONE',
                'label' => "N+1 (×{$maxDup}/req)",
                'severity' => $sev,
                'advice' => "Truy vấn lặp tới {$maxDup} lần trong 1 request. Nên dùng Eager Loading (`->with(...)`) hoặc gom thành 1 truy vấn `WHERE id IN (...)`.",
            ];
        }

        // 2. Slow query
        if ($maxMs >= $slowThresholdMs || $avgMs >= $slowThresholdMs) {
            $sev = ($avgMs >= $slowThresholdMs * 3 || $maxMs >= $slowThresholdMs * 5) ? 'critical' : 'high';
            $score += ($sev === 'critical' ? 35 : 22);
            $issues[] = [
                'code' => 'SLOW_QUERY',
                'label' => 'Slow (' . round($avgMs, 1) . 'ms TB)',
                'severity' => $sev,
                'advice' => 'Thời gian thực thi vượt ngưỡng chậm. Hãy chạy EXPLAIN để kiểm tra Full Table Scan / thiếu Index và thử tối ưu trong Benchmark.',
            ];
        }

        // 3. SELECT *
        if (preg_match('/^\s*SELECT\s+(\*|[`a-zA-Z0-9_]+\.\*)/i', $sql)) {
            $score += 12;
            $issues[] = [
                'code' => 'SELECT_STAR',
                'label' => 'SELECT *',
                'severity' => 'medium',
                'advice' => 'Đang lấy toàn bộ cột (`SELECT *`). Chỉ nên `SELECT` các cột thực sự dùng (tránh kéo cột TEXT/JSON lớn gây tốn RAM & I/O, đồng thời cho phép Covering Index).',
            ];
        }

        // 4. Leading wildcard LIKE '%...'
        if (preg_match('/\bLIKE\s+\?\b|\bLIKE\s+[\'"]%/i', (string) ($fp['sql_raw'] ?? $sql))) {
            $raw = (string) ($fp['sql_raw'] ?? $sql);
            if (preg_match('/\bLIKE\s+[\'"]%/i', $raw) || (stripos($raw, ' LIKE ') !== false && stripos((string) ($fp['sample_bindings'] ?? ''), '%') !== false)) {
                $score += 20;
                $issues[] = [
                    'code' => 'LEADING_WILDCARD',
                    'label' => "LIKE '%...'",
                    'severity' => 'high',
                    'advice' => 'Tìm kiếm `LIKE \'%keyword%\'` không thể sử dụng B-Tree Index (gây Full Table Scan). Cân nhắc tìm tiền tố `keyword%` hoặc dùng `FULLTEXT INDEX`.',
                ];
            }
        }

        // 5. Function wrapping column in WHERE (non-SARGable)
        if (preg_match('/\bWHERE\b.*?\b(DATE|YEAR|MONTH|DAY|LOWER|UPPER|TRIM|COALESCE|SUBSTRING|IFNULL)\s*\(/i', $sql)) {
            $score += 22;
            $issues[] = [
                'code' => 'FUNC_IN_WHERE',
                'label' => 'Hàm bọc cột WHERE',
                'severity' => 'high',
                'advice' => 'Dùng hàm (`DATE()`, `YEAR()`, `LOWER()`...) bọc cột trong `WHERE` sẽ vô hiệu hóa Index. Ví dụ thay `DATE(created_at) = ?` bằng `created_at >= ? AND created_at < ?`.',
            ];
        }

        // 6. ORDER BY RAND()
        if (preg_match('/\bORDER\s+BY\s+RAND\s*\(\s*\)/i', $sql)) {
            $score += 30;
            $issues[] = [
                'code' => 'ORDER_BY_RAND',
                'label' => 'ORDER BY RAND()',
                'severity' => 'critical',
                'advice' => '`ORDER BY RAND()` buộc MySQL quét và sort toàn bộ bảng. Nên random theo khoảng ID hoặc ở tầng PHP.',
            ];
        }

        // 7. Large OFFSET pagination
        if (preg_match('/\bOFFSET\s+(\d+)/i', (string) ($fp['sql_raw'] ?? ''), $om) && (int) $om[1] >= 500) {
            $score += 15;
            $issues[] = [
                'code' => 'LARGE_OFFSET',
                'label' => 'OFFSET lớn (' . $om[1] . ')',
                'severity' => 'medium',
                'advice' => 'Phân trang bằng `OFFSET` lớn buộc MySQL đọc và bỏ qua hàng nghìn dòng. Cân nhắc chuyển sang Cursor / Keyset Pagination (`WHERE id < last_id LIMIT ...`).',
            ];
        }

        // 8. High cumulative load
        if ($totalMs >= 1000 && $execCount >= 10) {
            $score += 15;
            $issues[] = [
                'code' => 'HIGH_TOTAL_LOAD',
                'label' => 'Tải cộng dồn cao (' . round($totalMs / 1000, 2) . 's)',
                'severity' => 'medium',
                'advice' => "Chạy {$execCount} lần gây tổng thời gian DB " . round($totalMs, 1) . 'ms. Cân nhắc cache kết quả (Redis/File Cache) nếu dữ liệu ít thay đổi.',
            ];
        }

        // Suggest candidate index columns from simple WHERE/ORDER BY patterns
        $indexSuggestion = $this->suggestIndex($sql);

        // Overall impact score combines static issues + total time impact
        $score += min(30, (int) round($totalMs / 200));

        $severity = 'low';
        if ($score >= 45) {
            $severity = 'critical';
        } elseif ($score >= 25) {
            $severity = 'high';
        } elseif ($score >= 10) {
            $severity = 'medium';
        }

        return [
            'score' => $score,
            'severity' => $severity,
            'issues' => $issues,
            'index_suggestion' => $indexSuggestion,
        ];
    }

    protected function suggestIndex(string $sql): ?string
    {
        if (!preg_match('/\bFROM\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $tm)) {
            return null;
        }
        $table = $tm[1];

        $cols = [];
        if (preg_match('/\bWHERE\b(.*?)(?:\bORDER\s+BY\b|\bGROUP\s+BY\b|\bLIMIT\b|$)/is', $sql, $wm)) {
            if (preg_match_all('/(?:`?[a-zA-Z0-9_]+`?\.)?`?([a-zA-Z0-9_]+)`?\s*(?:=|IN\b|>=|<=|>|<)/i', $wm[1], $cm)) {
                foreach ($cm[1] as $c) {
                    $lc = strtolower($c);
                    if (!in_array($lc, ['id', 'and', 'or', 'not', 'null', 'is', 'true', 'false'], true)) {
                        $cols[$c] = true;
                    }
                }
            }
        }
        if (preg_match('/\bORDER\s+BY\s+(?:`?[a-zA-Z0-9_]+`?\.)?`?([a-zA-Z0-9_]+)`?/i', $sql, $om)) {
            $c = $om[1];
            if (strtolower($c) !== 'id') {
                $cols[$c] = true;
            }
        }

        $colList = array_slice(array_keys($cols), 0, 3);
        if (empty($colList)) {
            return null;
        }

        $idxName = 'idx_' . $table . '_' . implode('_', $colList);
        $colSql = implode(', ', array_map(function ($c) {
            return "`{$c}`";
        }, $colList));

        return "CREATE INDEX `{$idxName}` ON `{$table}` ({$colSql});";
    }

    /**
     * Grade a page/endpoint from A (best) to F (needs urgent tuning).
     */
    public function gradePage(float $avgMsPerReq, float $queriesPerReq, int $slowQueries, int $n1Count): array
    {
        $penalty = 0;
        if ($avgMsPerReq >= 1000) $penalty += 45;
        elseif ($avgMsPerReq >= 400) $penalty += 30;
        elseif ($avgMsPerReq >= 150) $penalty += 15;
        elseif ($avgMsPerReq >= 50) $penalty += 5;

        if ($queriesPerReq >= 50) $penalty += 35;
        elseif ($queriesPerReq >= 20) $penalty += 20;
        elseif ($queriesPerReq >= 10) $penalty += 8;

        if ($n1Count > 0) $penalty += min(25, $n1Count * 8);
        if ($slowQueries > 0) $penalty += min(20, $slowQueries * 5);

        if ($penalty >= 65) return ['grade' => 'F', 'class' => 'badge-slow', 'label' => 'Rất chậm'];
        if ($penalty >= 45) return ['grade' => 'D', 'class' => 'badge-slow', 'label' => 'Chậm'];
        if ($penalty >= 25) return ['grade' => 'C', 'class' => 'badge-n1', 'label' => 'Cần tối ưu'];
        if ($penalty >= 10) return ['grade' => 'B', 'class' => 'badge-select', 'label' => 'Khá'];
        return ['grade' => 'A', 'class' => 'badge-ok', 'label' => 'Tốt'];
    }

    /**
     * Extract primary table name from a SQL query for River Flow visualization.
     */
    public static function extractTargetTable(string $sql): string
    {
        if (preg_match('/\b(?:FROM|INTO|UPDATE|TABLE)\s+(?:`?[a-zA-Z0-9_]+`?\.)?`?([a-zA-Z0-9_]+)`?/i', $sql, $m)) {
            $tbl = strtolower($m[1]);
            if (!in_array($tbl, ['select', 'where', 'set', 'values', 'dual'], true)) {
                return $tbl;
            }
        }
        return 'other';
    }

    /**
     * Build Real-Time River Flow topology (Endpoints -> River Branches -> DB Tables)
     * plus automatic Bottleneck Diagnosis & Actionable Solutions.
     */
    public function buildRiverTopology(array $flowRows, float $slowThresholdMs = 100.0): array
    {
        $sourceMap = [];
        $targetMap = [];
        $linkMap = [];
        $bottlenecks = [];
        $grandTotalMs = 0.0;

        foreach ($flowRows as $row) {
            $grandTotalMs += (float) $row['total_ms'];
        }
        $grandTotalMs = max(0.01, $grandTotalMs);

        foreach ($flowRows as $row) {
            $uri = (string) $row['uri'];
            $table = self::extractTargetTable((string) ($row['sql_normalized'] ?? $row['sql_raw'] ?? ''));
            $totalMs = (float) $row['total_ms'];
            $avgMs = (float) $row['avg_ms'];
            $maxMs = (float) $row['max_ms'];
            $exec = (int) $row['exec_count'];
            $slow = (int) $row['slow_count'];
            $maxDup = (int) $row['max_dup'];

            $analysis = $this->analyzeQuery($row, $slowThresholdMs);
            $health = 'ok';
            if ($analysis['severity'] === 'critical' || $slow > 0 || $maxMs >= $slowThresholdMs) {
                $health = 'bottleneck';
            } elseif ($analysis['severity'] === 'high' || $maxDup >= 3 || $avgMs >= ($slowThresholdMs * 0.5)) {
                $health = 'warn';
            }

            // Aggregate source (Endpoint)
            if (!isset($sourceMap[$uri])) {
                $sourceMap[$uri] = [
                    'id' => $uri,
                    'label' => $uri,
                    'method' => (string) ($row['http_method'] ?? 'GET'),
                    'total_ms' => 0.0,
                    'exec_count' => 0,
                    'slow_count' => 0,
                    'n1_count' => 0,
                    'health' => 'ok',
                ];
            }
            $sourceMap[$uri]['total_ms'] += $totalMs;
            $sourceMap[$uri]['exec_count'] += $exec;
            $sourceMap[$uri]['slow_count'] += $slow;
            if ($maxDup >= 3) $sourceMap[$uri]['n1_count']++;
            $sourceMap[$uri]['health'] = self::worseHealth($sourceMap[$uri]['health'], $health);

            // Aggregate target (DB Table)
            if (!isset($targetMap[$table])) {
                $targetMap[$table] = [
                    'id' => $table,
                    'label' => $table,
                    'total_ms' => 0.0,
                    'exec_count' => 0,
                    'slow_count' => 0,
                    'n1_count' => 0,
                    'health' => 'ok',
                ];
            }
            $targetMap[$table]['total_ms'] += $totalMs;
            $targetMap[$table]['exec_count'] += $exec;
            $targetMap[$table]['slow_count'] += $slow;
            if ($maxDup >= 3) $targetMap[$table]['n1_count']++;
            $targetMap[$table]['health'] = self::worseHealth($targetMap[$table]['health'], $health);

            // Aggregate link (URI -> Table)
            $linkKey = $uri . '=>' . $table;
            if (!isset($linkMap[$linkKey])) {
                $linkMap[$linkKey] = [
                    'source' => $uri,
                    'target' => $table,
                    'total_ms' => 0.0,
                    'exec_count' => 0,
                    'slow_count' => 0,
                    'max_ms' => 0.0,
                    'max_dup' => 1,
                    'health' => 'ok',
                    'top_sample_id' => (int) $row['sample_id'],
                    'top_sql_hash' => (string) $row['sql_hash'],
                    'top_ms' => 0.0,
                ];
            }
            $linkMap[$linkKey]['total_ms'] += $totalMs;
            $linkMap[$linkKey]['exec_count'] += $exec;
            $linkMap[$linkKey]['slow_count'] += $slow;
            $linkMap[$linkKey]['max_ms'] = max($linkMap[$linkKey]['max_ms'], $maxMs);
            $linkMap[$linkKey]['max_dup'] = max($linkMap[$linkKey]['max_dup'], $maxDup);
            $linkMap[$linkKey]['health'] = self::worseHealth($linkMap[$linkKey]['health'], $health);
            if ($totalMs >= $linkMap[$linkKey]['top_ms']) {
                $linkMap[$linkKey]['top_ms'] = $totalMs;
                $linkMap[$linkKey]['top_sample_id'] = (int) $row['sample_id'];
                $linkMap[$linkKey]['top_sql_hash'] = (string) $row['sql_hash'];
            }

            // Build bottleneck diagnosis card
            $sharePct = round(($totalMs / $grandTotalMs) * 100, 1);
            $solutions = $this->buildSolutionPlaybook($row, $analysis, $table);

            $bottlenecks[] = [
                'uri' => $uri,
                'table' => $table,
                'query_type' => (string) $row['query_type'],
                'sql_hash' => (string) $row['sql_hash'],
                'sample_id' => (int) $row['sample_id'],
                'sql_short' => mb_substr((string) $row['sql_normalized'], 0, 180),
                'caller' => (string) ($row['sample_caller'] ?: '-'),
                'exec_count' => $exec,
                'slow_count' => $slow,
                'avg_ms' => round($avgMs, 2),
                'max_ms' => round($maxMs, 2),
                'total_ms' => round($totalMs, 1),
                'max_dup' => $maxDup,
                'share_pct' => $sharePct,
                'health' => $health,
                'severity' => $analysis['severity'],
                'score' => $analysis['score'],
                'issues' => $analysis['issues'],
                'index_suggestion' => $analysis['index_suggestion'],
                'solutions' => $solutions,
            ];
        }

        // Sort and keep top 8 sources & top 8 targets for a crisp, readable river
        uasort($sourceMap, function ($a, $b) { return $b['total_ms'] <=> $a['total_ms']; });
        uasort($targetMap, function ($a, $b) { return $b['total_ms'] <=> $a['total_ms']; });
        $topSources = array_slice($sourceMap, 0, 8, true);
        $topTargets = array_slice($targetMap, 0, 8, true);

        $filteredLinks = [];
        foreach ($linkMap as $l) {
            if (isset($topSources[$l['source']], $topTargets[$l['target']])) {
                $l['total_ms'] = round($l['total_ms'], 1);
                $l['max_ms'] = round($l['max_ms'], 2);
                $l['avg_ms'] = round($l['total_ms'] / max(1, $l['exec_count']), 2);
                $filteredLinks[] = $l;
            }
        }
        usort($filteredLinks, function ($a, $b) { return $b['total_ms'] <=> $a['total_ms']; });

        usort($bottlenecks, function ($a, $b) {
            if ($b['score'] !== $a['score']) {
                return $b['score'] <=> $a['score'];
            }
            return $b['total_ms'] <=> $a['total_ms'];
        });

        // Round node totals
        foreach ($topSources as &$s) { $s['total_ms'] = round($s['total_ms'], 1); }
        unset($s);
        foreach ($topTargets as &$t) { $t['total_ms'] = round($t['total_ms'], 1); }
        unset($t);

        return [
            'sources' => array_values($topSources),
            'targets' => array_values($topTargets),
            'links' => array_slice($filteredLinks, 0, 24),
            'bottlenecks' => array_slice($bottlenecks, 0, 12),
            'grand_total_ms' => round($grandTotalMs, 1),
        ];
    }

    protected static function worseHealth(string $a, string $b): string
    {
        $rank = ['ok' => 0, 'warn' => 1, 'bottleneck' => 2];
        return ($rank[$b] ?? 0) > ($rank[$a] ?? 0) ? $b : $a;
    }

    protected function buildSolutionPlaybook(array $row, array $analysis, string $table): array
    {
        $solutions = [];
        $codes = array_column($analysis['issues'], 'code');

        if (in_array('N_PLUS_ONE', $codes, true)) {
            $solutions[] = [
                'tag' => 'SỬA CODE PHP (N+1)',
                'color' => 'warn',
                'text' => "Tại `{$row['sample_caller']}`: gom truy vấn lặp bằng Eager Loading `->with(...)` hoặc lấy 1 lần bằng `WHERE id IN (...)` thay vì gọi query trong vòng lặp.",
            ];
        }

        if (in_array('FUNC_IN_WHERE', $codes, true) || in_array('LEADING_WILDCARD', $codes, true) || in_array('ORDER_BY_RAND', $codes, true) || in_array('LARGE_OFFSET', $codes, true) || in_array('SELECT_STAR', $codes, true)) {
            $solutions[] = [
                'tag' => 'TUNING CÂU SQL',
                'color' => 'danger',
                'text' => 'Viết lại câu SQL: chỉ SELECT các cột cần dùng, bỏ hàm bọc cột trong WHERE (dùng khoảng >= / <), tránh LIKE "%..." hoặc OFFSET sâu — thử so sánh tốc độ ngay trong SQL Tuning Benchmark.',
            ];
        }

        if (!empty($analysis['index_suggestion']) && strtoupper((string) $row['query_type']) === 'SELECT' && (in_array('SLOW_QUERY', $codes, true) || (float) $row['avg_ms'] >= 40)) {
            $solutions[] = [
                'tag' => 'TẠO INDEX MYSQL',
                'color' => 'success',
                'text' => "Chạy EXPLAIN kiểm tra bảng `{$table}`. Nếu bị Full Table Scan (`type=ALL`), cân nhắc thêm index: `{$analysis['index_suggestion']}`",
            ];
        }

        if (in_array('HIGH_TOTAL_LOAD', $codes, true)) {
            $solutions[] = [
                'tag' => 'BỔ SUNG CACHE',
                'color' => 'info',
                'text' => "Truy vấn này chạy rất nhiều lần trên bảng `{$table}`. Cân nhắc bọc `Cache::remember(...)` (Redis/File) trong 1–5 phút nếu dữ liệu không cần tức thời tuyệt đối.",
            ];
        }

        if (empty($solutions)) {
            $solutions[] = [
                'tag' => 'THEO DÕI / EXPLAIN',
                'color' => 'info',
                'text' => "Truy vấn hiện ổn định. Nhấn Chi tiết → Chạy EXPLAIN để xác nhận đã dùng đúng index trên bảng `{$table}`.",
            ];
        }

        return $solutions;
    }

    /**
     * Safely benchmark & EXPLAIN two read-only SELECT queries (Original vs Tuned).
     */
    public function benchmarkCompare(string $connection, string $originalSql, string $tunedSql, int $runs = 2): array
    {
        $connName = $connection ?: config('database.default');
        $runs = max(1, min(5, $runs));

        foreach (['original' => $originalSql, 'tuned' => $tunedSql] as $label => $sql) {
            $t = trim($sql);
            if ($t === '') {
                return ['ok' => false, 'error' => "Câu lệnh {$label} không được để trống."];
            }
            if (!preg_match('/^SELECT\b/i', $t)) {
                return ['ok' => false, 'error' => "Chỉ cho phép Benchmark an toàn trên câu lệnh SELECT ({$label})."];
            }
            if (strpos(rtrim($t, '; '), ';') !== false) {
                return ['ok' => false, 'error' => "Không cho phép nhiều câu lệnh (;) trong {$label}."];
            }
        }

        $originalSql = rtrim(trim($originalSql), ';');
        $tunedSql = rtrim(trim($tunedSql), ';');

        try {
            $origResult = $this->runSingleBenchmark($connName, $originalSql, $runs);
            $tunedResult = $this->runSingleBenchmark($connName, $tunedSql, $runs);

            $origMs = max(0.01, $origResult['avg_ms']);
            $tunedMs = max(0.01, $tunedResult['avg_ms']);
            $diffPct = round((($origMs - $tunedMs) / $origMs) * 100, 1);
            $speedupX = round($origMs / $tunedMs, 2);

            return [
                'ok' => true,
                'original' => $origResult,
                'tuned' => $tunedResult,
                'comparison' => [
                    'faster' => $tunedMs < $origMs,
                    'diff_pct' => $diffPct,
                    'speedup_x' => $speedupX,
                    'rows_saved' => max(0, $origResult['explain_rows'] - $tunedResult['explain_rows']),
                ],
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => 'Lỗi khi chạy Benchmark: ' . $e->getMessage()];
        }
    }

    protected function runSingleBenchmark(string $connName, string $sql, int $runs): array
    {
        $explain = $this->explainAnalyzer->explain($connName, $sql, []);
        $explainRows = 0;
        $usesFullScan = false;
        if (!empty($explain['plan'])) {
            foreach ($explain['plan'] as $step) {
                $explainRows += (int) ($step['rows'] ?? 0);
                if (strtoupper((string) ($step['type'] ?? '')) === 'ALL') {
                    $usesFullScan = true;
                }
            }
        }

        $times = [];
        $resultCount = 0;
        $pdo = DB::connection($connName)->getPdo();

        for ($i = 0; $i < $runs; $i++) {
            $t0 = microtime(true);
            $stmt = $pdo->query($sql);
            $rows = $stmt ? $stmt->fetchAll(\PDO::FETCH_NUM) : [];
            $times[] = (microtime(true) - $t0) * 1000.0;
            $resultCount = count($rows);
        }

        return [
            'avg_ms' => round(array_sum($times) / count($times), 2),
            'min_ms' => round(min($times), 2),
            'max_ms' => round(max($times), 2),
            'result_rows' => $resultCount,
            'explain_rows' => $explainRows,
            'uses_full_scan' => $usesFullScan,
            'plan' => $explain['plan'] ?? [],
            'hints' => $explain['hints'] ?? [],
        ];
    }
}
