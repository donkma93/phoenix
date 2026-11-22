<?php

namespace Modules\QueryMonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\QueryMonitor\Analyzer\ExplainAnalyzer;
use Modules\QueryMonitor\Analyzer\TuningAnalyzer;
use Modules\QueryMonitor\Storage\MonitorStore;

class DashboardController extends Controller
{
    /** @var MonitorStore */
    protected $store;

    /** @var ExplainAnalyzer */
    protected $analyzer;

    /** @var TuningAnalyzer */
    protected $tuning;

    public function __construct(MonitorStore $store, ExplainAnalyzer $analyzer, TuningAnalyzer $tuning)
    {
        $this->store = $store;
        $this->analyzer = $analyzer;
        $this->tuning = $tuning;
    }

    protected function resolveRange(string $range): array
    {
        $map = [
            '1h' => ['label' => '1 giờ qua', 'seconds' => 3600],
            '6h' => ['label' => '6 giờ qua', 'seconds' => 21600],
            '24h' => ['label' => '24 giờ qua', 'seconds' => 86400],
            '7d' => ['label' => '7 ngày qua', 'seconds' => 604800],
            'all' => ['label' => 'Tất cả', 'seconds' => 0],
        ];
        if (!isset($map[$range])) {
            $range = '24h';
        }
        $since = $map[$range]['seconds'] > 0
            ? gmdate('Y-m-d H:i:s', time() - $map[$range]['seconds'])
            : '1970-01-01 00:00:00';

        return [$range, $since, $map];
    }

    public function index(Request $request)
    {
        [$range, $since, $ranges] = $this->resolveRange((string) $request->query('range', '24h'));
        $sortBy = (string) $request->query('sort', 'total_ms');

        $stats = $this->store->getOverviewStats($since);
        $topFingerprints = $this->store->getTopFingerprints($since, $sortBy, 20);
        $topRoutes = $this->store->getTopRoutes($since, 15);
        $n1Hotspots = $this->store->getN1Hotspots($since, (int) config('query-monitor.duplicate_threshold', 3), 12);

        return view('query-monitor::dashboard.index', [
            'range' => $range,
            'ranges' => $ranges,
            'sortBy' => $sortBy,
            'stats' => $stats,
            'topFingerprints' => $topFingerprints,
            'topRoutes' => $topRoutes,
            'n1Hotspots' => $n1Hotspots,
            'recordingEnabled' => $this->store->isRecordingEnabled(),
            'slowThresholdMs' => $this->store->getSlowThresholdMs((float) config('query-monitor.slow_threshold_ms', 100)),
        ]);
    }

    public function queries(Request $request)
    {
        [$range, $since, $ranges] = $this->resolveRange((string) $request->query('range', '24h'));

        $filters = [
            'since' => $since,
            'q' => trim((string) $request->query('q', '')),
            'uri' => trim((string) $request->query('uri', '')),
            'query_type' => (string) $request->query('query_type', ''),
            'min_ms' => $request->query('min_ms', ''),
            'slow_only' => $request->boolean('slow_only'),
            'n1_only' => $request->boolean('n1_only'),
            'sort' => (string) $request->query('sort', 'latest'),
        ];

        $page = max(1, (int) $request->query('page', 1));
        $result = $this->store->searchQueryLogs($filters, $page, 50);
        foreach ($result['items'] as &$item) {
            $item['created_at'] = MonitorStore::toDisplayTime($item['created_at'] ?? null);
        }
        unset($item);

        return view('query-monitor::dashboard.queries', [
            'range' => $range,
            'ranges' => $ranges,
            'filters' => $filters,
            'result' => $result,
            'recordingEnabled' => $this->store->isRecordingEnabled(),
        ]);
    }

    public function fingerprint(string $hash)
    {
        $detail = $this->store->getFingerprintDetail($hash);
        if (!$detail) {
            abort(404, 'Không tìm thấy mẫu truy vấn này.');
        }

        $detail['first_seen'] = MonitorStore::toDisplayTime($detail['first_seen'] ?? null);
        $detail['last_seen'] = MonitorStore::toDisplayTime($detail['last_seen'] ?? null);
        foreach ($detail['samples'] as &$s) {
            $s['created_at'] = MonitorStore::toDisplayTime($s['created_at'] ?? null);
        }
        unset($s);

        return view('query-monitor::dashboard.fingerprint', [
            'detail' => $detail,
            'recordingEnabled' => $this->store->isRecordingEnabled(),
        ]);
    }

    public function explain(int $id)
    {
        $row = $this->store->getQueryById($id);
        if (!$row) {
            return response()->json(['ok' => false, 'error' => 'Không tìm thấy bản ghi query.'], 404);
        }

        $bindings = json_decode((string) $row['bindings_json'], true);
        if (!is_array($bindings)) {
            $bindings = [];
        }

        $result = $this->analyzer->explain($row['connection'], $row['sql_raw'], $bindings);
        return response()->json($result);
    }

    public function live()
    {
        return view('query-monitor::dashboard.live', [
            'recordingEnabled' => $this->store->isRecordingEnabled(),
            'slowThresholdMs' => $this->store->getSlowThresholdMs((float) config('query-monitor.slow_threshold_ms', 100)),
        ]);
    }

    public function liveData(Request $request)
    {
        $windowKey = (string) $request->query('window', '5m');
        $windowMap = [
            '1m' => ['seconds' => 60, 'bucket' => 2],
            '5m' => ['seconds' => 300, 'bucket' => 10],
            '15m' => ['seconds' => 900, 'bucket' => 30],
            '1h' => ['seconds' => 3600, 'bucket' => 120],
        ];
        if (!isset($windowMap[$windowKey])) {
            $windowKey = '5m';
        }
        $winSec = $windowMap[$windowKey]['seconds'];
        $bucketSec = $windowMap[$windowKey]['bucket'];
        $sinceIso = gmdate('Y-m-d H:i:s', time() - $winSec);

        $slowThreshold = $this->store->getSlowThresholdMs((float) config('query-monitor.slow_threshold_ms', 100));
        $stats = $this->store->getOverviewStats($sinceIso);
        $series = $this->store->getTimeSeries($winSec, $bucketSec);
        $types = $this->store->getTypeBreakdown($sinceIso);
        $flowRows = $this->store->getRiverFlowRows($sinceIso, 120);
        $river = $this->tuning->buildRiverTopology($flowRows, $slowThreshold);
        $recent = $this->store->getRecentQueries(60, [
            'since' => $sinceIso,
            'slow_only' => $request->boolean('slow_only'),
            'n1_only' => $request->boolean('n1_only'),
            'q' => trim((string) $request->query('q', '')),
        ]);
        foreach ($recent as &$r) {
            $r['created_at'] = MonitorStore::toDisplayTime($r['created_at'] ?? null);
        }
        unset($r);

        return response()->json([
            'server_time' => MonitorStore::nowDisplay('H:i:s'),
            'timezone' => 'GMT+7',
            'window' => $windowKey,
            'window_seconds' => $winSec,
            'bucket_seconds' => $bucketSec,
            'recording_enabled' => $this->store->isRecordingEnabled(),
            'slow_threshold_ms' => $slowThreshold,
            'stats' => [
                'total_queries' => (int) ($stats['total_queries'] ?? 0),
                'slow_queries' => (int) ($stats['slow_queries'] ?? 0),
                'avg_ms' => round((float) ($stats['avg_ms'] ?? 0), 2),
                'max_ms' => round((float) ($stats['max_ms'] ?? 0), 2),
                'total_ms' => round((float) ($stats['total_ms'] ?? 0), 2),
                'total_requests' => (int) ($stats['total_requests'] ?? 0),
                'n1_hotspots' => (int) ($stats['n1_hotspots'] ?? 0),
                'qps' => round(((int) ($stats['total_queries'] ?? 0)) / max(1, $winSec), 2),
            ],
            'river' => $river,
            'series' => $series,
            'types' => $types,
            'recent' => $recent,
        ]);
    }

    public function queryDetail(int $id)
    {
        $row = $this->store->getQueryById($id);
        if (!$row) {
            return response()->json(['ok' => false, 'error' => 'Không tìm thấy truy vấn này.'], 404);
        }

        $bindings = json_decode((string) $row['bindings_json'], true);
        if (!is_array($bindings)) {
            $bindings = [];
        }

        $interpolated = $this->interpolateSql($row['sql_raw'], $bindings);
        $row['created_at'] = MonitorStore::toDisplayTime($row['created_at'] ?? null);
        $siblings = $this->store->getQueriesByRequestId((string) $row['request_id'], 100);
        $requestTotalMs = 0.0;
        foreach ($siblings as &$s) {
            $requestTotalMs += (float) $s['time_ms'];
            $s['created_at'] = MonitorStore::toDisplayTime($s['created_at'] ?? null);
        }
        unset($s);

        return response()->json([
            'ok' => true,
            'query' => $row,
            'bindings' => $bindings,
            'sql_interpolated' => $interpolated,
            'fingerprint_url' => route('query-monitor.fingerprint', $row['sql_hash']),
            'request_summary' => [
                'request_id' => $row['request_id'],
                'query_count' => count($siblings),
                'total_ms' => round($requestTotalMs, 2),
                'queries' => $siblings,
            ],
        ]);
    }

    protected function interpolateSql(string $sql, array $bindings): string
    {
        if (empty($bindings)) {
            return $sql;
        }
        $idx = 0;
        return (string) preg_replace_callback('/\?/', function () use ($bindings, &$idx) {
            if (!array_key_exists($idx, $bindings)) {
                return '?';
            }
            $val = $bindings[$idx++];
            if ($val === null) {
                return 'NULL';
            }
            if (is_bool($val)) {
                return $val ? '1' : '0';
            }
            if (is_int($val) || is_float($val)) {
                return (string) $val;
            }
            return "'" . str_replace("'", "\\'", (string) $val) . "'";
        }, $sql);
    }

    public function tables()
    {
        $info = $this->analyzer->inspectDatabase();

        return view('query-monitor::dashboard.tables', [
            'info' => $info,
            'recordingEnabled' => $this->store->isRecordingEnabled(),
        ]);
    }

    public function settings()
    {
        return view('query-monitor::dashboard.settings', [
            'recordingEnabled' => $this->store->isRecordingEnabled(),
            'slowThresholdMs' => $this->store->getSlowThresholdMs((float) config('query-monitor.slow_threshold_ms', 100)),
            'sqlitePath' => config('query-monitor.sqlite_path'),
            'sqliteSizeBytes' => is_file(config('query-monitor.sqlite_path')) ? filesize(config('query-monitor.sqlite_path')) : 0,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'recording_enabled' => 'required|in:0,1',
            'slow_threshold_ms' => 'required|numeric|min:1|max:60000',
        ]);

        $this->store->setSetting('recording_enabled', (string) $data['recording_enabled']);
        $this->store->setSetting('slow_threshold_ms', (string) $data['slow_threshold_ms']);

        return back()->with('status', 'Đã lưu cấu hình Query Monitor.');
    }

    public function purge(Request $request)
    {
        $days = $request->input('days');
        $deleted = $this->store->purgeLogs($days !== null && $days !== '' ? (int) $days : null);

        return back()->with('status', "Đã xóa {$deleted} bản ghi log truy vấn.");
    }
}
