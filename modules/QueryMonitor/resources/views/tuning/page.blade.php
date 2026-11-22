@extends('query-monitor::layout')
@section('title', 'Tuning Trang: ' . $uri)

@section('content')
<div style="margin-bottom:14px;">
    <a href="{{ route('query-monitor.tuning', ['range' => $range]) }}">&larr; Quay lại SQL Tuning Workbench</a>
</div>

<div class="card">
    <div class="flex-between">
        <div>
            <h1 style="margin:0;font-size:19px;color:#fff;">Phân tích truy vấn của trang: <code>{{ $uri }}</code></h1>
            <div class="muted" style="font-size:13px;margin-top:4px;">
                Tổng {{ number_format($totalExec) }} lượt truy vấn &bull; Tổng thời gian DB: <strong>{{ number_format($totalMs, 1) }} ms</strong> &bull; {{ count($fingerprints) }} mẫu SQL khác nhau
            </div>
        </div>
        <form method="GET" class="filters" style="margin:0;">
            <input type="hidden" name="uri" value="{{ $uri }}">
            <select name="range" onchange="this.form.submit()">
                @foreach($ranges as $key => $info)
                    <option value="{{ $key }}" {{ $range === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="card">
    <h2>Tất cả mẫu truy vấn trên trang này (sắp xếp theo tỷ trọng thời gian DB)</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tỷ trọng</th>
                    <th>Mẫu SQL, Lỗi &amp; Gợi ý cân đối</th>
                    <th class="num">Lượt chạy</th>
                    <th class="num">TB / Max</th>
                    <th class="num">Tổng ms</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fingerprints as $fp)
                    @php
                        $pct = $totalMs > 0 ? round(($fp['total_ms'] * 100) / $totalMs, 1) : 0;
                        $a = $fp['analysis'];
                    @endphp
                    <tr>
                        <td style="white-space:nowrap;">
                            <strong>{{ $pct }}%</strong>
                            <div style="width:72px;height:6px;background:#0b1220;border-radius:999px;overflow:hidden;margin-top:4px;">
                                <div style="width:{{ min(100, $pct) }}%;height:100%;background:{{ $pct >= 30 ? '#f87171' : '#38bdf8' }};"></div>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;flex-wrap:wrap;margin-bottom:5px;">
                                <span class="badge badge-{{ strtolower($fp['query_type']) }}">{{ $fp['query_type'] }}</span>
                                @foreach($a['issues'] as $iss)
                                    <span class="badge {{ $iss['severity'] === 'critical' ? 'badge-slow' : 'badge-n1' }}">{{ $iss['label'] }}</span>
                                @endforeach
                            </div>
                            <a href="{{ route('query-monitor.fingerprint', $fp['sql_hash']) }}">
                                <div class="sql-code">{{ \Illuminate\Support\Str::limit($fp['sql_normalized'], 260) }}</div>
                            </a>
                            <div class="muted" style="font-size:11.5px;margin-top:4px;">
                                Caller: <code>{{ $fp['sample_caller'] ?: '-' }}</code>
                            </div>
                            @if(!empty($a['issues']))
                                <ul style="margin:6px 0 0;padding-left:16px;font-size:12px;color:#cbd5e1;">
                                    @foreach($a['issues'] as $iss)
                                        <li>{{ $iss['advice'] }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if(!empty($a['index_suggestion']) && $fp['query_type'] === 'SELECT')
                                <div style="margin-top:6px;font-size:11.5px;color:#a7f3d0;font-family:monospace;">
                                    Gợi ý Index: {{ $a['index_suggestion'] }}
                                </div>
                            @endif
                        </td>
                        <td class="num">
                            {{ number_format($fp['exec_count']) }}
                            @if((int)$fp['max_dup'] >= 3)
                                <div><span class="badge badge-n1">&times;{{ $fp['max_dup'] }}/req</span></div>
                            @endif
                        </td>
                        <td class="num" style="white-space:nowrap;">
                            <div>{{ number_format($fp['avg_ms'], 2) }} ms</div>
                            <div class="muted" style="font-size:11.5px;">Max: {{ number_format($fp['max_ms'], 2) }}</div>
                        </td>
                        <td class="num"><strong>{{ number_format($fp['total_ms'], 1) }}</strong></td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="{{ route('query-monitor.fingerprint', $fp['sql_hash']) }}">EXPLAIN</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted" style="text-align:center;padding:24px;">Không có truy vấn nào cho trang này.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
