@extends('query-monitor::layout')
@section('title', 'Phân tích mẫu truy vấn')

@section('content')
<div style="margin-bottom:14px;">
    <a href="{{ route('query-monitor.dashboard') }}">&larr; Quay lại Tổng quan</a>
</div>

<div class="card">
    <div class="flex-between" style="margin-bottom:12px;">
        <h2 style="margin:0;">
            <span class="badge badge-{{ strtolower($detail['query_type']) }}">{{ $detail['query_type'] }}</span>
            Chi tiết mẫu truy vấn (Hash: <code>{{ substr($detail['sql_hash'], 0, 10) }}</code>)
        </h2>
        <span class="muted">Connection: <strong>{{ $detail['connection'] }}</strong></span>
    </div>
    <div class="sql-code" style="max-width:none;font-size:13px;padding:12px;">{{ $detail['sql_normalized'] }}</div>
</div>

<div class="kpi-grid">
    <div class="kpi">
        <div class="label">Tổng số lần chạy</div>
        <div class="value">{{ number_format($detail['exec_count']) }}</div>
        <div class="sub">Chậm: {{ number_format($detail['slow_count']) }} lần</div>
    </div>
    <div class="kpi">
        <div class="label">Trung bình</div>
        <div class="value">{{ number_format($detail['avg_ms'], 2) }} ms</div>
        <div class="sub">Min: {{ number_format($detail['min_ms'], 2) }} ms</div>
    </div>
    <div class="kpi danger">
        <div class="label">Chậm nhất (Max)</div>
        <div class="value">{{ number_format($detail['max_ms'], 2) }} ms</div>
        <div class="sub">Tổng thời gian: {{ number_format($detail['total_ms'], 1) }} ms</div>
    </div>
    <div class="kpi {{ (int)$detail['max_dup'] >= 3 ? 'warning' : 'success' }}">
        <div class="label">Lặp tối đa / Request</div>
        <div class="value">&times;{{ $detail['max_dup'] }}</div>
        <div class="sub">{{ (int)$detail['max_dup'] >= 3 ? 'Có dấu hiệu N+1 query' : 'Bình thường' }}</div>
    </div>
</div>

<div class="card" id="explain-card" style="display:none;">
    <h2>Kết quả phân tích EXPLAIN &amp; Gợi ý tối ưu</h2>
    <div id="explain-body"></div>
</div>

<div class="card">
    <h2>Vị trí code gọi truy vấn này (Callers)</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>File : Dòng</th>
                    <th>Endpoint</th>
                    <th class="num">Số lần</th>
                    <th class="num">TB (ms)</th>
                    <th class="num">Max (ms)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail['callers'] as $c)
                    <tr>
                        <td><code>{{ $c['caller'] }}</code></td>
                        <td>{{ $c['uri'] }}</td>
                        <td class="num">{{ number_format($c['cnt']) }}</td>
                        <td class="num">{{ number_format($c['avg_ms'], 2) }}</td>
                        <td class="num">{{ number_format($c['max_ms'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>Các lần thực thi chậm nhất (Top 20 mẫu)</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Thời gian (GMT+7)</th>
                    <th>SQL thực tế &amp; Bindings</th>
                    <th>Endpoint / Caller</th>
                    <th class="num">Thời gian</th>
                    <th>Phân tích</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail['samples'] as $s)
                    <tr>
                        <td style="white-space:nowrap;" class="muted">{{ $s['created_at'] }}</td>
                        <td>
                            <div class="sql-code">{{ $s['sql_raw'] }}</div>
                            @if(!empty($s['bindings_json']) && $s['bindings_json'] !== '[]')
                                <div class="muted" style="font-size:11px;margin-top:4px;">Bindings: <code>{{ $s['bindings_json'] }}</code></div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $s['http_method'] }} {{ $s['uri'] }}</div>
                            <div class="muted" style="font-size:11px;">{{ $s['caller'] ?: '-' }}</div>
                        </td>
                        <td class="num"><strong>{{ number_format((float)$s['time_ms'], 2) }} ms</strong></td>
                        <td>
                            @if(strtoupper($s['query_type']) === 'SELECT')
                                <button type="button" class="btn btn-primary btn-sm" onclick="runExplain({{ (int)$s['id'] }})">Chạy EXPLAIN</button>
                            @else
                                <span class="muted" style="font-size:12px;">Chỉ SELECT</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
async function runExplain(id) {
    const card = document.getElementById('explain-card');
    const body = document.getElementById('explain-body');
    card.style.display = 'block';
    body.innerHTML = '<div class="muted">Đang chạy EXPLAIN trên MySQL...</div>';
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });

    try {
        const res = await fetch(`{{ url(config('query-monitor.route_prefix', 'query-monitor') . '/explain') }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        if (!data.ok) {
            body.innerHTML = `<div class="alert alert-danger">${escapeHtml(data.error || 'Không thể chạy EXPLAIN')}</div>`;
            return;
        }

        let html = '';
        (data.hints || []).forEach(h => {
            html += `<div class="alert alert-${h.level}">${escapeHtml(h.message)}</div>`;
        });

        if (data.plan && data.plan.length > 0) {
            const cols = Object.keys(data.plan[0]);
            html += '<div class="table-wrap"><table><thead><tr>' + cols.map(c => `<th>${escapeHtml(c)}</th>`).join('') + '</tr></thead><tbody>';
            data.plan.forEach(row => {
                html += '<tr>' + cols.map(c => `<td><code>${escapeHtml(row[c] === null ? 'NULL' : String(row[c]))}</code></td>`).join('') + '</tr>';
            });
            html += '</tbody></table></div>';
        }
        body.innerHTML = html;
    } catch (e) {
        body.innerHTML = `<div class="alert alert-danger">Lỗi kết nối: ${escapeHtml(e.message)}</div>`;
    }
}
function escapeHtml(s) {
    return s.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}
</script>
@endsection
