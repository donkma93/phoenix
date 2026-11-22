<?php

namespace Modules\QueryMonitor\Analyzer;

use Illuminate\Support\Facades\DB;

class ExplainAnalyzer
{
    /**
     * Safely run EXPLAIN on a SELECT query and produce Vietnamese optimization hints.
     */
    public function explain(string $connection, string $sql, array $bindings = []): array
    {
        $trimmed = trim($sql);
        if (!preg_match('/^SELECT\b/i', $trimmed)) {
            return [
                'ok' => false,
                'error' => 'Chỉ hỗ trợ chạy EXPLAIN an toàn trên câu lệnh SELECT.',
            ];
        }

        if (strpos($trimmed, ';') !== false) {
            return [
                'ok' => false,
                'error' => 'Không hỗ trợ EXPLAIN cho câu lệnh chứa nhiều statement (;).',
            ];
        }

        try {
            $rows = DB::connection($connection ?: config('database.default'))
                ->select('EXPLAIN ' . $trimmed, $bindings);

            $plan = array_map(function ($r) {
                return (array) $r;
            }, $rows);

            return [
                'ok' => true,
                'plan' => $plan,
                'hints' => $this->buildHints($plan),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'error' => 'Lỗi khi chạy EXPLAIN: ' . $e->getMessage(),
            ];
        }
    }

    protected function buildHints(array $plan): array
    {
        $hints = [];

        foreach ($plan as $step) {
            $table = $step['table'] ?? '?';
            $type = strtoupper((string) ($step['type'] ?? ''));
            $key = $step['key'] ?? null;
            $rows = (int) ($step['rows'] ?? 0);
            $extra = (string) ($step['Extra'] ?? '');

            if ($type === 'ALL') {
                $hints[] = [
                    'level' => 'danger',
                    'message' => "Bảng `{$table}` đang bị FULL TABLE SCAN (type = ALL, ước tính quét ~" . number_format($rows) . " dòng). Nên tạo index cho các cột lọc trong WHERE / JOIN.",
                ];
            } elseif ($type === 'INDEX' && $rows > 1000) {
                $hints[] = [
                    'level' => 'warning',
                    'message' => "Bảng `{$table}` đang quét toàn bộ cây index (type = INDEX, ~" . number_format($rows) . " dòng). Kiểm tra lại điều kiện WHERE.",
                ];
            }

            if (empty($key) && $table !== '' && $table[0] !== '<') {
                $hints[] = [
                    'level' => 'warning',
                    'message' => "Bảng `{$table}` không sử dụng index nào (key = NULL).",
                ];
            }

            if (stripos($extra, 'Using filesort') !== false) {
                $hints[] = [
                    'level' => 'warning',
                    'message' => "Bảng `{$table}` phát sinh `Using filesort` — cân nhắc thêm composite index bao gồm cả cột lọc WHERE và cột ORDER BY.",
                ];
            }

            if (stripos($extra, 'Using temporary') !== false) {
                $hints[] = [
                    'level' => 'warning',
                    'message' => "Bảng `{$table}` phải tạo bảng tạm (`Using temporary`) khi GROUP BY / DISTINCT — cân nhắc tối ưu index theo cột nhóm.",
                ];
            }

            if ($rows > 50000 && $type !== 'ALL') {
                $hints[] = [
                    'level' => 'info',
                    'message' => "Bảng `{$table}` ước tính duyệt nhiều dòng (~" . number_format($rows) . " dòng) dù đã dùng index `{$key}` — kiểm tra độ chọn lọc (selectivity) của index.",
                ];
            }
        }

        if (empty($hints)) {
            $hints[] = [
                'level' => 'success',
                'message' => 'Kế hoạch thực thi (EXPLAIN) trông ổn: đã dùng index và không thấy dấu hiệu Full Table Scan hay Filesort.',
            ];
        }

        return $hints;
    }

    /**
     * Inspect tables, sizes, row estimates, and indexes in the target MySQL database.
     */
    public function inspectDatabase(?string $connection = null): array
    {
        $connName = $connection ?: config('database.default');
        $dbName = config("database.connections.{$connName}.database");

        $tables = DB::connection($connName)->select("
            SELECT
                TABLE_NAME AS table_name,
                ENGINE AS engine,
                TABLE_ROWS AS approx_rows,
                ROUND(DATA_LENGTH / 1024 / 1024, 2) AS data_mb,
                ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS index_mb,
                ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
            ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
        ", [$dbName]);

        $indexesRaw = DB::connection($connName)->select("
            SELECT
                TABLE_NAME AS table_name,
                INDEX_NAME AS index_name,
                NON_UNIQUE AS non_unique,
                GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX SEPARATOR ', ') AS columns
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
            GROUP BY TABLE_NAME, INDEX_NAME, NON_UNIQUE
            ORDER BY TABLE_NAME, INDEX_NAME
        ", [$dbName]);

        $indexesByTable = [];
        foreach ($indexesRaw as $idx) {
            $indexesByTable[$idx->table_name][] = (array) $idx;
        }

        $result = [];
        foreach ($tables as $t) {
            $row = (array) $t;
            $row['indexes'] = $indexesByTable[$t->table_name] ?? [];
            $result[] = $row;
        }

        return [
            'database' => $dbName,
            'connection' => $connName,
            'tables' => $result,
        ];
    }
}
