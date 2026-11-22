@extends('query-monitor::layout')
@section('title', 'SQL Tuning Workbench')

@section('content')
<style>
    .sev-critical { background: rgba(248,113,113,0.22); color: #fca5a5; border: 1px solid rgba(248,113,113,0.45); }
    .sev-high { background: rgba(251,146,60,0.22); color: #fdba74; border: 1px solid rgba(251,146,60,0.45); }
    .sev-medium { background: rgba(251,191,36,0.18); color: #fde68a; border: 1px solid rgba(251,191,36,0.4); }
    .sev-low { background: rgba(148,163,184,0.16); color: #cbd5e1; }
    .advice-list { margin: 8px 0 0; padding-left: 16px; font-size: 12.5px; color: #cbd5e1; }
    .advice-list li { margin-bottom: 4px; }
    .idx-hint {
        margin-top: 8px; padding: 7px 10px; border-radius: 6px;
        background: rgba(52,211,153,0.10); border: 1px solid rgba(52,211,153,0.3);
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 11.5px; color: #a7f3d0; word-break: break-all;
    }
    textarea.sql-editor {
        width: 100%; min-height: 120px; padding: 10px 12px;
        background: #0b1220; border: 1px solid #334155; border-radius: 7px;
        color: #e2e8f0; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 12.5px; line-height: 1.5; resize: vertical;
    }
    .bench-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 900px) { .bench-grid { grid-template-columns: 1fr; } }
    .grade-pill {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border-radius: 7px; font-weight: 800; font-size: 14px;
    }
</style>

<div class="flex-between" style="margin-bottom: 18px;">
    <div>
        <h1 style="margin:0;font-size:20px;color:#fff;">SQL Tuning Workbench — Phát hiện &amp; Cân đối truy vấn</h1>
        <div class="muted" style="font-size:13px;">Tự động phát hiện anti-pattern (N+1, Full Scan, SELECT *, LIKE '%...', hàm bọc WHERE), chấm điểm từng trang và so sánh Benchmark Trước/Sau.</div>
    </div>
    <form method="GET" class="filters" style="margin:0;">
        <select name="range" onchange="this.form.submit()">
            @foreach($ranges as $key => $info)
                <option value="{{ $key }}" {{ $range === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
            <option value="open" {{ $statusFilter === 'open' ? 'selected' : '' }}>Chưa xử lý</option>
            <option value="in_progress" {{ $statusFilter === 'in_progress' ? 'selected' : '' }}>Đang tối ưu</option>
            <option value="resolved" {{ $statusFilter === 'resolved' ? 'selected' : '' }}>Đã tối ưu</option>
        </select>
    </form>
</div>

<div class="kpi-grid">
    <div class="kpi {{ $summaryCounts['critical'] > 0 ? 'danger' : 'success' }}">
        <div class="label">Nghiêm trọng (Critical)</div>
        <div class="value">{{ $summaryCounts['critical'] }}</div>
        <div class="sub">Cần xử lý ưu tiên ngay</div>
    </div>
    <div class="kpi {{ $summaryCounts['high'] > 0 ? 'warning' : 'success' }}">
        <div class="label">Mức cao (High)</div>
        <div class="value">{{ $summaryCounts['high'] }}</div>
        <div class="sub">N+1 / Slow / Mất index</div>
    </div>
    <div class="kpi">
        <div class="label">Mức trung bình (Medium)</div>
        <div class="value">{{ $summaryCounts['medium'] }}</div>
        <div class="sub">SELECT * / OFFSET lớn / Tải cao</div>
    </div>
    <div class="kpi success">
        <div class="label">Đã đánh dấu tối ưu</div>
        <div class="value">{{ $summaryCounts['resolved'] }}</div>
        <div class="sub">Theo dõi tiến độ cải thiện</div>
    </div>
</div>

{{-- 1. Page-level Health Grading --}}
<div class="card">
    <div class="flex-between" style="margin-bottom:12px;">
        <div>
            <h2 style="margin:0;">1. Xếp hạng hiệu năng theo từng Trang / Endpoint</h2>
            <div class="muted" style="font-size:12px;">Chấm điểm từ <strong>A</strong> (tốt) đến <strong>F</strong> (rất chậm). Nhấn vào trang để xem toàn bộ truy vấn cần cân đối của trang đó.</div>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Điểm</th>
                    <th>Trang / Endpoint</th>
                    <th class="num">Requests</th>
                    <th class="num">Query / Req</th>
                    <th class="num">TB ms / Req</th>
                    <th class="num">Slow</th>
                    <th class="num">Mẫu N+1</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $p)
                    <tr>
                        <td>
                            <span class="grade-pill {{ $p['grade']['class'] }}" title="{{ $p['grade']['label'] }}">{{ $p['grade']['grade'] }}</span>
                        </td>
                        <td>
                            <span class="badge badge-other">{{ $p['http_method'] }}</span>
                            <a href="{{ route('query-monitor.tuning.page', ['uri' => $p['uri'], 'range' => $range]) }}">
                                <strong>{{ $p['uri'] }}</strong>
                            </a>
                        </td>
                        <td class="num">{{ number_format($p['request_count']) }}</td>
                        <td class="num" style="color:{{ $p['queries_per_req'] >= 20 ? 'var(--warning)' : 'inherit' }}"><strong>{{ $p['queries_per_req'] }}</strong></td>
                        <td class="num" style="color:{{ $p['avg_ms_per_req'] >= 400 ? 'var(--danger)' : 'inherit' }}"><strong>{{ number_format($p['avg_ms_per_req'], 1) }} ms</strong></td>
                        <td class="num">
                            @if((int)$p['slow_queries'] > 0)
                                <span class="badge badge-slow">{{ $p['slow_queries'] }}</span>
                            @else
                                <span class="muted">0</span>
                            @endif
                        </td>
                        <td class="num">
                            @if((int)$p['n1_patterns'] > 0)
                                <span class="badge badge-n1">{{ $p['n1_patterns'] }}</span>
                            @else
                                <span class="muted">0</span>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-sm" href="{{ route('query-monitor.tuning.page', ['uri' => $p['uri'], 'range' => $range]) }}">Phân tích trang &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="muted" style="text-align:center;padding:24px;">Chưa có dữ liệu trang nào trong khoảng thời gian này.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 2. Benchmark Sandbox (Before vs After) --}}
<div class="card" id="benchmark-section">
    <div class="flex-between" style="margin-bottom:12px;">
        <div>
            <h2 style="margin:0;">2. Phòng thí nghiệm Benchmark SQL (So sánh Trước vs Sau tối ưu)</h2>
            <div class="muted" style="font-size:12px;">Chạy thử <code>EXPLAIN</code> &amp; đo thời gian thực tế song song giữa câu SQL gốc và câu SQL đã tối ưu (chỉ cho phép <code>SELECT</code> an toàn).</div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <label class="muted" style="font-size:12px;">Số lần chạy lấy TB:</label>
            <select id="bench-runs">
                <option value="1">1 lần</option>
                <option value="2" selected>2 lần</option>
                <option value="3">3 lần</option>
            </select>
            <button type="button" class="btn btn-primary" id="btn-run-bench">Chạy so sánh EXPLAIN &amp; Tốc độ</button>
        </div>
    </div>

    <div class="bench-grid">
        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#fca5a5;">SQL GỐC (TRƯỚC KHI TỐI ƯU)</label>
            <textarea id="bench-orig" class="sql-editor" placeholder="Chọn 'Đưa vào Benchmark' ở bảng dưới hoặc dán câu SELECT gốc vào đây..."></textarea>
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#6ee7b7;">SQL ĐÃ CÂN ĐỐI (SAU KHI TỐI ƯU)</label>
            <textarea id="bench-tuned" class="sql-editor" placeholder="Viết lại câu SELECT tối ưu hơn (bỏ SELECT *, bỏ hàm bọc WHERE, thêm điều kiện lọc...) để so sánh..."></textarea>
        </div>
    </div>

    <div id="bench-result" style="margin-top:16px;display:none;"></div>
</div>

{{-- 3. Automated Query Tuning Queue --}}
<div class="card">
    <h2>3. Danh sách Truy vấn cần cân đối (Ưu tiên theo mức ảnh hưởng)</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mức độ</th>
                    <th>Truy vấn, Vấn đề phát hiện &amp; Gợi ý tối ưu</th>
                    <th class="num">Hiệu năng</th>
                    <th style="min-width:220px;">Trạng thái &amp; Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($queue as $item)
                    @php
                        $a = $item['analysis'];
                        $sev = $a['severity'];
                    @endphp
                    <tr>
                        <td style="white-space:nowrap;">
                            <span class="badge sev-{{ $sev }}">{{ strtoupper($sev) }}</span>
                            <div class="muted" style="font-size:11px;margin-top:4px;">Score: {{ $a['score'] }}</div>
                            <div style="margin-top:4px;"><span class="badge badge-{{ strtolower($item['query_type']) }}">{{ $item['query_type'] }}</span></div>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;flex-wrap:wrap;margin-bottom:6px;">
                                @forelse($a['issues'] as $iss)
                                    <span class="badge sev-{{ $iss['severity'] }}">{{ $iss['label'] }}</span>
                                @empty
                                    <span class="badge badge-ok">Không có anti-pattern rõ ràng</span>
                                @endforelse
                            </div>

                            <a href="{{ route('query-monitor.fingerprint', $item['sql_hash']) }}">
                                <div class="sql-code">{{ \Illuminate\Support\Str::limit($item['sql_normalized'], 260) }}</div>
                            </a>
                            <div class="muted" style="font-size:11.5px;margin-top:4px;">
                                Endpoint: <strong>{{ $item['sample_uri'] ?: 'CLI' }}</strong> &bull; Caller: <code>{{ $item['sample_caller'] ?: '-' }}</code>
                            </div>

                            @if(!empty($a['issues']))
                                <ul class="advice-list">
                                    @foreach($a['issues'] as $iss)
                                        <li>{{ $iss['advice'] }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            @if(!empty($a['index_suggestion']) && $item['query_type'] === 'SELECT')
                                <div class="idx-hint">
                                    <strong>Gợi ý Index tham khảo:</strong> {{ $a['index_suggestion'] }}
                                </div>
                            @endif
                        </td>
                        <td class="num" style="white-space:nowrap;">
                            <div>Tổng: <strong>{{ number_format($item['total_ms'], 1) }} ms</strong></div>
                            <div class="muted">TB: {{ number_format($item['avg_ms'], 2) }} ms</div>
                            <div style="color:{{ $item['max_ms'] >= $slowThresholdMs ? 'var(--danger)' : 'var(--muted)' }}">Max: {{ number_format($item['max_ms'], 2) }} ms</div>
                            <div class="muted">{{ number_format($item['exec_count']) }} lần</div>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                                @if($item['query_type'] === 'SELECT')
                                    <button type="button" class="btn btn-primary btn-sm"
                                            onclick="loadToBenchmark({{ (int)$item['sample_id'] }})">Đưa vào Benchmark</button>
                                @endif
                                <a class="btn btn-sm" href="{{ route('query-monitor.fingerprint', $item['sql_hash']) }}">EXPLAIN</a>
                            </div>

                            <form method="POST" action="{{ route('query-monitor.tuning.note') }}" style="display:flex;flex-direction:column;gap:6px;">
                                @csrf
                                <input type="hidden" name="sql_hash" value="{{ $item['sql_hash'] }}">
                                <input type="hidden" name="before_ms" value="{{ round($item['avg_ms'], 2) }}">
                                <select name="status" style="font-size:12px;padding:5px 8px;">
                                    <option value="open" {{ $item['tuning_status'] === 'open' ? 'selected' : '' }}>Chưa xử lý</option>
                                    <option value="in_progress" {{ $item['tuning_status'] === 'in_progress' ? 'selected' : '' }}>Đang tối ưu</option>
                                    <option value="resolved" {{ $item['tuning_status'] === 'resolved' ? 'selected' : '' }}>Đã tối ưu</option>
                                </select>
                                <input type="text" name="note" value="{{ $item['tuning_note'] ?? '' }}" placeholder="Ghi chú cách sửa / index..." style="font-size:12px;padding:5px 8px;">
                                <button type="submit" class="btn btn-sm">Lưu ghi chú</button>
                            </form>
                            @if(!empty($item['note_updated_at']))
                                <div class="muted" style="font-size:11px;margin-top:4px;">Cập nhật: {{ $item['note_updated_at'] }}</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted" style="text-align:center;padding:26px;">Chưa có truy vấn nào cần tối ưu trong bộ lọc này.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
const QUERY_API = @json(url(config('query-monitor.route_prefix', 'query-monitor') . '/api/query'));
const BENCH_API = @json(route('query-monitor.tuning.benchmark'));
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

async function loadToBenchmark(queryId) {
    try {
        const res = await fetch(`${QUERY_API}/${queryId}`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data.ok) return alert(data.error || 'Không tải được SQL');
        document.getElementById('bench-orig').value = data.sql_interpolated;
        document.getElementById('bench-tuned').value = data.sql_interpolated;
        document.getElementById('benchmark-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (e) {
        alert('Lỗi: ' + e.message);
    }
}

document.getElementById('btn-run-bench').addEventListener('click', async () => {
    const orig = document.getElementById('bench-orig').value.trim();
    const tuned = document.getElementById('bench-tuned').value.trim();
    const runs = Number(document.getElementById('bench-runs').value || 2);
    const box = document.getElementById('bench-result');

    if (!orig || !tuned) {
        box.style.display = 'block';
        box.innerHTML = '<div class="alert alert-warning">Vui lòng nhập cả SQL gốc và SQL đã cân đối.</div>';
        return;
    }

    box.style.display = 'block';
    box.innerHTML = '<div class="alert alert-info">Đang chạy EXPLAIN &amp; đo tốc độ trên MySQL...</div>';

    try {
        const res = await fetch(BENCH_API, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ original_sql: orig, tuned_sql: tuned, runs })
        });
        const data = await res.json();
        if (!data.ok) {
            box.innerHTML = `<div class="alert alert-danger">${esc(data.error || 'Benchmark thất bại')}</div>`;
            return;
        }

        const c = data.comparison;
        const o = data.original;
        const t = data.tuned;
        const bannerClass = c.faster ? 'alert-success' : 'alert-warning';
        const bannerText = c.faster
            ? `SQL sau tối ưu NHANH HƠN ${c.diff_pct}% (${c.speedup_x}x): từ ${o.avg_ms} ms xuống ${t.avg_ms} ms`
            : `SQL sau tối ưu chậm hơn hoặc tương đương (${c.diff_pct}%): gốc ${o.avg_ms} ms vs mới ${t.avg_ms} ms`;

        function renderPlanTable(plan) {
            if (!plan || !plan.length) return '<div class="muted">Không có kế hoạch EXPLAIN.</div>';
            const cols = Object.keys(plan[0]);
            return '<div class="table-wrap" style="border:1px solid #334155;border-radius:6px;"><table><thead><tr>'
                + cols.map(col => `<th>${esc(col)}</th>`).join('')
                + '</tr></thead><tbody>'
                + plan.map(row => '<tr>' + cols.map(col => `<td><code>${esc(row[col] === null ? 'NULL' : row[col])}</code></td>`).join('') + '</tr>').join('')
                + '</tbody></table></div>';
        }

        box.innerHTML = `
            <div class="alert ${bannerClass}"><strong>${bannerText}</strong> ${c.rows_saved > 0 ? `&bull; Giảm ước tính <strong>${c.rows_saved.toLocaleString()}</strong> dòng quét!` : ''}</div>
            <div class="bench-grid">
                <div style="background:#0b1220;border:1px solid #334155;border-radius:8px;padding:14px;">
                    <div style="font-weight:700;color:#fca5a5;margin-bottom:8px;">TRƯỚC TỐI ƯU</div>
                    <div style="margin-bottom:10px;font-size:13px;">
                        TB: <strong>${o.avg_ms} ms</strong> (Min ${o.min_ms} / Max ${o.max_ms}) &bull;
                        Số dòng trả về: <strong>${o.result_rows}</strong> &bull;
                        EXPLAIN rows: <strong>${o.explain_rows.toLocaleString()}</strong>
                        ${o.uses_full_scan ? '<span class="badge badge-slow">FULL SCAN</span>' : ''}
                    </div>
                    ${renderPlanTable(o.plan)}
                </div>
                <div style="background:#0b1220;border:1px solid #334155;border-radius:8px;padding:14px;">
                    <div style="font-weight:700;color:#6ee7b7;margin-bottom:8px;">SAU TỐI ƯU</div>
                    <div style="margin-bottom:10px;font-size:13px;">
                        TB: <strong>${t.avg_ms} ms</strong> (Min ${t.min_ms} / Max ${t.max_ms}) &bull;
                        Số dòng trả về: <strong>${t.result_rows}</strong> &bull;
                        EXPLAIN rows: <strong>${t.explain_rows.toLocaleString()}</strong>
                        ${t.uses_full_scan ? '<span class="badge badge-slow">FULL SCAN</span>' : '<span class="badge badge-ok">INDEXED</span>'}
                    </div>
                    ${renderPlanTable(t.plan)}
                </div>
            </div>
        `;
    } catch (e) {
        box.innerHTML = `<div class="alert alert-danger">Lỗi kết nối: ${esc(e.message)}</div>`;
    }
});
</script>
@endsection
