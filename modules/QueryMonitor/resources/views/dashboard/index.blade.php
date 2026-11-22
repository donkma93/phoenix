@extends('query-monitor::layout')
@section('title', 'Tổng quan hiệu năng DB')

@section('content')
<div class="flex-between" style="margin-bottom: 18px;">
    <div>
        <h1 style="margin:0;font-size:20px;color:#fff;">Tổng quan hiệu năng truy vấn</h1>
        <div class="muted" style="font-size:13px;">Ngưỡng Slow Query hiện tại: <strong>{{ $slowThresholdMs }} ms</strong> &bull; Trạng thái ghi: <strong>{{ $recordingEnabled ? 'Đang bật' : 'Đang tạm dừng' }}</strong></div>
    </div>
    <form method="GET" class="filters" style="margin:0;">
        <select name="range" onchange="this.form.submit()">
            @foreach($ranges as $key => $info)
                <option value="{{ $key }}" {{ $range === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
            @endforeach
        </select>
        <select name="sort" onchange="this.form.submit()">
            <option value="total_ms" {{ $sortBy === 'total_ms' ? 'selected' : '' }}>Sắp xếp: Tổng thời gian</option>
            <option value="avg_ms" {{ $sortBy === 'avg_ms' ? 'selected' : '' }}>Sắp xếp: Thời gian TB</option>
            <option value="max_ms" {{ $sortBy === 'max_ms' ? 'selected' : '' }}>Sắp xếp: Thời gian Max</option>
            <option value="exec_count" {{ $sortBy === 'exec_count' ? 'selected' : '' }}>Sắp xếp: Số lần chạy</option>
        </select>
    </form>
</div>

<div class="kpi-grid">
    <div class="kpi">
        <div class="label">Tổng truy vấn</div>
        <div class="value">{{ number_format((int)($stats['total_queries'] ?? 0)) }}</div>
        <div class="sub">{{ number_format((int)($stats['total_requests'] ?? 0)) }} requests &bull; {{ number_format((int)($stats['unique_patterns'] ?? 0)) }} mẫu SQL</div>
    </div>
    <div class="kpi {{ ((int)($stats['slow_queries'] ?? 0)) > 0 ? 'danger' : 'success' }}">
        <div class="label">Truy vấn chậm (&ge; {{ $slowThresholdMs }}ms)</div>
        <div class="value">{{ number_format((int)($stats['slow_queries'] ?? 0)) }}</div>
        <div class="sub">
            @php $tq = max(1, (int)($stats['total_queries'] ?? 0)); @endphp
            {{ round(((int)($stats['slow_queries'] ?? 0)) * 100 / $tq, 2) }}% tổng truy vấn
        </div>
    </div>
    <div class="kpi">
        <div class="label">Thời gian TB / Max</div>
        <div class="value">{{ number_format((float)($stats['avg_ms'] ?? 0), 2) }} ms</div>
        <div class="sub">Max: {{ number_format((float)($stats['max_ms'] ?? 0), 2) }} ms &bull; Tổng: {{ number_format((float)($stats['total_ms'] ?? 0) / 1000, 2) }} s</div>
    </div>
    <div class="kpi {{ ((int)($stats['n1_hotspots'] ?? 0)) > 0 ? 'warning' : 'success' }}">
        <div class="label">Cảnh báo N+1 / Lặp query</div>
        <div class="value">{{ number_format((int)($stats['n1_hotspots'] ?? 0)) }}</div>
        <div class="sub">SQLite log: {{ number_format(((int)($stats['db_size_bytes'] ?? 0)) / 1048576, 2) }} MB</div>
    </div>
</div>

<div class="card">
    <div class="flex-between" style="margin-bottom:12px;">
        <h2 style="margin:0;">Top nhóm truy vấn tốn tài nguyên nhất (Query Fingerprints)</h2>
        <a href="{{ route('query-monitor.queries', ['slow_only' => 1]) }}" class="btn btn-sm">Xem tất cả Slow Queries &rarr;</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Loại</th>
                    <th>Mẫu truy vấn (nhấn để phân tích &amp; chạy EXPLAIN)</th>
                    <th class="num">Số lần</th>
                    <th class="num">Chậm</th>
                    <th class="num">TB (ms)</th>
                    <th class="num">Max (ms)</th>
                    <th class="num">Tổng (ms)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topFingerprints as $fp)
                    <tr>
                        <td><span class="badge badge-{{ strtolower($fp['query_type']) }}">{{ $fp['query_type'] }}</span></td>
                        <td>
                            <a href="{{ route('query-monitor.fingerprint', $fp['sql_hash']) }}" style="display:block;">
                                <div class="sql-code">{{ \Illuminate\Support\Str::limit($fp['sql_normalized'], 220) }}</div>
                            </a>
                            @if((int)$fp['max_dup'] >= 3)
                                <span class="badge badge-n1" style="margin-top:4px;">Lặp tối đa {{ $fp['max_dup'] }} lần/request (N+1)</span>
                            @endif
                        </td>
                        <td class="num">{{ number_format($fp['exec_count']) }}</td>
                        <td class="num">
                            @if((int)$fp['slow_count'] > 0)
                                <span class="badge badge-slow">{{ number_format($fp['slow_count']) }}</span>
                            @else
                                <span class="muted">0</span>
                            @endif
                        </td>
                        <td class="num">{{ number_format($fp['avg_ms'], 2) }}</td>
                        <td class="num" style="color:{{ $fp['max_ms'] >= $slowThresholdMs ? 'var(--danger)' : 'inherit' }}">{{ number_format($fp['max_ms'], 2) }}</td>
                        <td class="num"><strong>{{ number_format($fp['total_ms'], 1) }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="muted" style="text-align:center;padding:28px;">Chưa có dữ liệu truy vấn trong khoảng thời gian này. Hãy truy cập các trang trong hệ thống chính để bắt đầu ghi nhận.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(480px, 1fr));gap:20px;">
    <div class="card">
        <h2>Route / Endpoint tốn thời gian DB nhất</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Endpoint</th>
                        <th class="num">Reqs</th>
                        <th class="num">Q/Req</th>
                        <th class="num">TB ms/Req</th>
                        <th class="num">Tổng ms</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topRoutes as $r)
                        <tr>
                            <td>
                                <span class="badge badge-other">{{ $r['http_method'] }}</span>
                                <a href="{{ route('query-monitor.queries', ['uri' => $r['uri']]) }}">{{ \Illuminate\Support\Str::limit($r['uri'], 55) }}</a>
                            </td>
                            <td class="num">{{ number_format($r['request_count']) }}</td>
                            <td class="num">{{ $r['queries_per_req'] }}</td>
                            <td class="num">{{ number_format($r['avg_ms_per_req'], 1) }}</td>
                            <td class="num"><strong>{{ number_format($r['total_ms'], 1) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">Chưa có dữ liệu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2>Điểm nóng N+1 / Truy vấn lặp trong cùng 1 Request</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>URI &amp; Caller</th>
                        <th class="num">Lặp</th>
                        <th class="num">Tổng ms</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($n1Hotspots as $h)
                        <tr>
                            <td>
                                <a href="{{ route('query-monitor.fingerprint', $h['sql_hash']) }}">
                                    <strong>{{ \Illuminate\Support\Str::limit($h['uri'] ?: 'CLI', 45) }}</strong>
                                </a>
                                <div class="muted" style="font-size:11px;">{{ $h['caller'] ?: 'Unknown caller' }}</div>
                                <div class="muted" style="font-size:11px;font-family:monospace;">{{ \Illuminate\Support\Str::limit($h['sql_normalized'], 90) }}</div>
                            </td>
                            <td class="num"><span class="badge badge-n1">&times;{{ $h['dup_count'] }}</span></td>
                            <td class="num">{{ number_format($h['total_ms'], 1) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="muted">Không phát hiện điểm nóng N+1 nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
