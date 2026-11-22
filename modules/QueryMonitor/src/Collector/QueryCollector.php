<?php

namespace Modules\QueryMonitor\Collector;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Str;
use Modules\QueryMonitor\Storage\MonitorStore;

class QueryCollector
{
    /** @var MonitorStore */
    protected $store;

    /** @var string */
    protected $requestId;

    /** @var array */
    protected $buffer = [];

    /** @var array<string, int> */
    protected $hashCounts = [];

    /** @var bool */
    protected $recording = true;

    /** @var float */
    protected $slowThresholdMs = 100.0;

    /** @var bool */
    protected $initialized = false;

    /** @var bool */
    protected $ignored = false;

    public function __construct(MonitorStore $store)
    {
        $this->store = $store;
        $this->requestId = (string) Str::uuid();
    }

    protected function initOnce(): void
    {
        if ($this->initialized) {
            return;
        }
        $this->initialized = true;

        try {
            $this->recording = $this->store->isRecordingEnabled();
            $this->slowThresholdMs = $this->store->getSlowThresholdMs(
                (float) config('query-monitor.slow_threshold_ms', 100)
            );
        } catch (\Throwable $e) {
            $this->recording = false;
        }

        if (!app()->runningInConsole()) {
            $path = ltrim((string) request()->path(), '/');
            $ignoredPaths = (array) config('query-monitor.ignored_paths', ['query-monitor']);
            foreach ($ignoredPaths as $prefix) {
                $prefix = ltrim((string) $prefix, '/');
                if ($prefix !== '' && ($path === $prefix || strpos($path, $prefix . '/') === 0)) {
                    $this->ignored = true;
                    break;
                }
            }
        }
    }

    public function record(QueryExecuted $query): void
    {
        $this->initOnce();

        if (!$this->recording || $this->ignored) {
            return;
        }

        $sqlRaw = trim($query->sql);
        if ($sqlRaw === '') {
            return;
        }

        $normalized = self::normalizeSql($sqlRaw);
        $hash = md5($normalized);
        $this->hashCounts[$hash] = ($this->hashCounts[$hash] ?? 0) + 1;

        $queryType = self::detectQueryType($sqlRaw);
        $timeMs = (float) $query->time;

        $httpMethod = app()->runningInConsole() ? 'CLI' : strtoupper((string) request()->method());
        $uri = app()->runningInConsole()
            ? ('artisan ' . implode(' ', array_slice((array) ($_SERVER['argv'] ?? []), 1)))
            : ('/' . ltrim((string) request()->path(), '/'));

        $route = (!app()->runningInConsole() && request()->route()) ? request()->route()->getName() : null;

        $safeBindings = [];
        foreach ((array) $query->bindings as $b) {
            if ($b instanceof \DateTimeInterface) {
                $safeBindings[] = $b->format('Y-m-d H:i:s');
            } elseif (is_scalar($b) || $b === null) {
                $safeBindings[] = is_string($b) && strlen($b) > 500 ? substr($b, 0, 500) . '...' : $b;
            } else {
                $safeBindings[] = (string) $b;
            }
        }

        $this->buffer[] = [
            'request_id' => $this->requestId,
            'connection' => (string) $query->connectionName,
            'sql_raw' => $sqlRaw,
            'sql_normalized' => $normalized,
            'sql_hash' => $hash,
            'bindings_json' => json_encode($safeBindings, JSON_UNESCAPED_UNICODE),
            'query_type' => $queryType,
            'time_ms' => round($timeMs, 3),
            'is_slow' => $timeMs >= $this->slowThresholdMs ? 1 : 0,
            'duplicate_in_request' => 1,
            'http_method' => $httpMethod,
            'uri' => mb_substr($uri, 0, 255),
            'route_name' => $route,
            'caller' => $this->findCaller(),
            'created_at' => gmdate('Y-m-d H:i:s'),
        ];
    }

    public function flush(): void
    {
        if (empty($this->buffer)) {
            return;
        }

        $rows = $this->buffer;
        $hashCounts = $this->hashCounts;
        $this->buffer = [];
        $this->hashCounts = [];

        foreach ($rows as &$row) {
            $row['duplicate_in_request'] = $hashCounts[$row['sql_hash']] ?? 1;
        }
        unset($row);

        try {
            $this->store->insertBatch($rows);
        } catch (\Throwable $e) {
            // Never break the main application if monitor logging fails
        }
    }

    public static function normalizeSql(string $sql): string
    {
        $s = preg_replace('/\s+/', ' ', trim($sql));
        $s = preg_replace("/'(?:\\\\.|[^'\\\\])*'/", '?', $s);
        $s = preg_replace('/"(?:\\\\.|[^"\\\\])*"/', '?', $s);
        $s = preg_replace('/\b\d+(?:\.\d+)?\b/', '?', $s);
        $s = preg_replace('/\bIN\s*\(\s*\?(?:\s*,\s*\?)*\s*\)/i', 'IN (?)', $s);
        return trim($s);
    }

    public static function detectQueryType(string $sql): string
    {
        if (preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE|REPLACE|CREATE|ALTER|DROP|SHOW|EXPLAIN|CALL)\b/i', $sql, $m)) {
            return strtoupper($m[1]);
        }
        return 'OTHER';
    }

    protected function findCaller(): ?string
    {
        $basePath = str_replace('\\', '/', base_path()) . '/';
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 30);

        foreach ($trace as $frame) {
            if (empty($frame['file'])) {
                continue;
            }
            $file = str_replace('\\', '/', $frame['file']);
            if (
                strpos($file, '/vendor/') !== false ||
                strpos($file, '/modules/QueryMonitor/') !== false ||
                strpos($file, '/storage/framework/views/') !== false
            ) {
                continue;
            }
            $relative = strpos($file, $basePath) === 0 ? substr($file, strlen($basePath)) : $file;
            $line = $frame['line'] ?? 0;
            return "{$relative}:{$line}";
        }

        return null;
    }
}
