<?php

namespace Modules\QueryMonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\QueryMonitor\Analyzer\TuningAnalyzer;
use Modules\QueryMonitor\Storage\MonitorStore;

class TuningController extends Controller
{
    /** @var MonitorStore */
    protected $store;

    /** @var TuningAnalyzer */
    protected $tuning;

    public function __construct(MonitorStore $store, TuningAnalyzer $tuning)
    {
        $this->store = $store;
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
        $statusFilter = (string) $request->query('status', 'all');
        $slowThreshold = $this->store->getSlowThresholdMs((float) config('query-monitor.slow_threshold_ms', 100));

        // 1. Page-level grading
        $rawPages = $this->store->getPageTuningSummary($since, 25);
        $pages = [];
        foreach ($rawPages as $p) {
            $grade = $this->tuning->gradePage(
                (float) $p['avg_ms_per_req'],
                (float) $p['queries_per_req'],
                (int) $p['slow_queries'],
                (int) $p['n1_patterns']
            );
            $p['grade'] = $grade;
            $pages[] = $p;
        }

        // 2. Query-level tuning queue with automated static analysis
        $rawQueue = $this->store->getTuningQueue($since, 60);
        $queue = [];
        $summaryCounts = ['critical' => 0, 'high' => 0, 'medium' => 0, 'resolved' => 0];

        foreach ($rawQueue as $item) {
            $analysis = $this->tuning->analyzeQuery($item, $slowThreshold);
            $item['analysis'] = $analysis;
            $item['tuning_status'] = $item['tuning_status'] ?: 'open';
            $item['note_updated_at'] = MonitorStore::toDisplayTime($item['note_updated_at'] ?? null);

            if ($item['tuning_status'] === 'resolved') {
                $summaryCounts['resolved']++;
            } elseif (isset($summaryCounts[$analysis['severity']])) {
                $summaryCounts[$analysis['severity']]++;
            }

            if ($statusFilter !== 'all' && $item['tuning_status'] !== $statusFilter) {
                continue;
            }
            $queue[] = $item;
        }

        usort($queue, function ($a, $b) {
            return $b['analysis']['score'] <=> $a['analysis']['score'];
        });

        return view('query-monitor::tuning.index', [
            'range' => $range,
            'ranges' => $ranges,
            'statusFilter' => $statusFilter,
            'slowThresholdMs' => $slowThreshold,
            'pages' => $pages,
            'queue' => array_slice($queue, 0, 35),
            'summaryCounts' => $summaryCounts,
            'recordingEnabled' => $this->store->isRecordingEnabled(),
        ]);
    }

    public function pageDetail(Request $request)
    {
        $uri = (string) $request->query('uri', '');
        if ($uri === '') {
            return redirect()->route('query-monitor.tuning');
        }

        [$range, $since, $ranges] = $this->resolveRange((string) $request->query('range', '24h'));
        $slowThreshold = $this->store->getSlowThresholdMs((float) config('query-monitor.slow_threshold_ms', 100));

        $rawFps = $this->store->getPageFingerprints($uri, $since);
        $fingerprints = [];
        $totalMs = 0.0;
        $totalExec = 0;

        foreach ($rawFps as $fp) {
            $fp['analysis'] = $this->tuning->analyzeQuery($fp, $slowThreshold);
            $fp['tuning_status'] = $fp['tuning_status'] ?: 'open';
            $totalMs += (float) $fp['total_ms'];
            $totalExec += (int) $fp['exec_count'];
            $fingerprints[] = $fp;
        }

        return view('query-monitor::tuning.page', [
            'uri' => $uri,
            'range' => $range,
            'ranges' => $ranges,
            'slowThresholdMs' => $slowThreshold,
            'fingerprints' => $fingerprints,
            'totalMs' => $totalMs,
            'totalExec' => $totalExec,
            'recordingEnabled' => $this->store->isRecordingEnabled(),
        ]);
    }

    public function benchmark(Request $request)
    {
        $data = $request->validate([
            'original_sql' => 'required|string',
            'tuned_sql' => 'required|string',
            'connection' => 'nullable|string',
            'runs' => 'nullable|integer|min:1|max:5',
        ]);

        $res = $this->tuning->benchmarkCompare(
            (string) ($data['connection'] ?? config('database.default')),
            $data['original_sql'],
            $data['tuned_sql'],
            (int) ($data['runs'] ?? 2)
        );

        return response()->json($res);
    }

    public function saveNote(Request $request)
    {
        $data = $request->validate([
            'sql_hash' => 'required|string|max:64',
            'status' => 'required|in:open,in_progress,resolved',
            'note' => 'nullable|string|max:2000',
            'before_ms' => 'nullable|numeric',
            'after_ms' => 'nullable|numeric',
        ]);

        $this->store->saveTuningNote(
            $data['sql_hash'],
            $data['status'],
            (string) ($data['note'] ?? ''),
            isset($data['before_ms']) && $data['before_ms'] !== '' ? (float) $data['before_ms'] : null,
            isset($data['after_ms']) && $data['after_ms'] !== '' ? (float) $data['after_ms'] : null
        );

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Đã lưu ghi chú & trạng thái tối ưu.');
    }
}
