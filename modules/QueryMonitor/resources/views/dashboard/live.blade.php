@extends('query-monitor::layout')
@section('title', 'Real-time Monitor & Biểu đồ')

@section('content')
<style>
    @keyframes pulseLive {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }
    @keyframes rowFlash {
        0% { background: rgba(56, 189, 248, 0.28); }
        100% { background: transparent; }
    }
    .live-pill {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;
        background: rgba(52, 211, 153, 0.14); border: 1px solid rgba(52, 211, 153, 0.4); color: #6ee7b7;
    }
    .live-pill.paused {
        background: rgba(251, 191, 36, 0.14); border-color: rgba(251, 191, 36, 0.4); color: #fde68a;
    }
    .live-pill .pulse {
        width: 8px; height: 8px; border-radius: 50%; background: #34d399;
        animation: pulseLive 1.4s infinite ease-in-out;
    }
    .live-pill.paused .pulse { background: #fbbf24; animation: none; }
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 20px;
    }
    @media (max-width: 1024px) {
        .charts-grid { grid-template-columns: 1fr; }
    }
    .chart-box { position: relative; width: 100%; height: 240px; }
    .chart-box canvas { width: 100%; height: 100%; display: block; cursor: crosshair; }
    .chart-tooltip {
        position: absolute; pointer-events: none; display: none; z-index: 10;
        background: rgba(15, 23, 42, 0.96); border: 1px solid #475569; border-radius: 8px;
        padding: 8px 11px; font-size: 12px; color: #f8fafc; box-shadow: 0 8px 24px rgba(0,0,0,0.45);
        white-space: nowrap;
    }
    .legend { display: flex; gap: 14px; font-size: 12px; color: var(--muted); align-items: center; flex-wrap: wrap; }
    .legend-item { display: inline-flex; align-items: center; gap: 6px; }
    .legend-swatch { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
    .stream-row { cursor: pointer; transition: background 0.15s; }
    .stream-row.is-new { animation: rowFlash 1.4s ease-out; }
    .stream-row.selected td { background: rgba(56, 189, 248, 0.12); }
    .type-grid { display: grid; grid-template-columns: 180px 1fr; gap: 18px; align-items: center; }
    @media (max-width: 640px) { .type-grid { grid-template-columns: 1fr; } }

    /* Slide-over Detail Drawer */
    .drawer-backdrop {
        position: fixed; inset: 0; background: rgba(2, 6, 23, 0.65); z-index: 50;
        display: none; justify-content: flex-end;
    }
    .drawer-backdrop.open { display: flex; }
    .drawer {
        width: min(760px, 96vw); height: 100vh; background: #0f172a;
        border-left: 1px solid #334155; display: flex; flex-direction: column;
        box-shadow: -16px 0 48px rgba(0,0,0,0.55);
    }
    .drawer-header {
        padding: 16px 22px; background: #1e293b; border-bottom: 1px solid #334155;
        display: flex; justify-content: space-between; align-items: center; gap: 12px;
    }
    .drawer-body { padding: 20px 22px; overflow-y: auto; flex: 1; }
    .meta-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 10px; margin-bottom: 16px;
    }
    .meta-item {
        background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 10px 12px;
    }
    .meta-item .k { font-size: 11px; color: var(--muted); text-transform: uppercase; }
    .meta-item .v { font-size: 13px; color: #fff; font-weight: 600; margin-top: 3px; word-break: break-all; }
    .wf-bar-wrap { width: 100%; height: 6px; background: #1e293b; border-radius: 999px; overflow: hidden; margin-top: 4px; }
    .wf-bar { height: 100%; border-radius: 999px; background: #38bdf8; }
    .wf-bar.slow { background: #f87171; }

    /* River Flow Stage */
    .river-stage {
        position: relative; width: 100%; height: 390px;
        background: radial-gradient(circle at 50% 45%, rgba(14,165,233,0.08), rgba(11,18,32,0.96) 72%);
        border: 1px solid #263449; border-radius: 10px; overflow: hidden;
    }
    .river-stage canvas { width: 100%; height: 100%; display: block; cursor: pointer; }
    .river-headers {
        display: flex; justify-content: space-between; padding: 0 12px 8px;
        font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); font-weight: 700;
    }
    .bn-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 14px; }
    @media (max-width: 640px) { .bn-grid { grid-template-columns: 1fr; } }
    .bn-card {
        background: #0b1220; border: 1px solid #334155; border-radius: 9px; padding: 14px 16px;
        border-left: 4px solid #38bdf8;
    }
    .bn-card.health-bottleneck { border-left-color: #f87171; }
    .bn-card.health-warn { border-left-color: #fbbf24; }
    .sol-item {
        margin-top: 8px; padding: 8px 10px; border-radius: 7px; font-size: 12.5px;
        background: rgba(30,41,59,0.75); border: 1px solid #293548;
    }
    .sol-tag {
        display: inline-block; padding: 2px 7px; border-radius: 5px; font-size: 10.5px;
        font-weight: 700; margin-right: 6px; letter-spacing: 0.03em;
    }
    .sol-tag.danger { background: rgba(248,113,113,0.22); color: #fca5a5; }
    .sol-tag.warn { background: rgba(251,191,36,0.22); color: #fde68a; }
    .sol-tag.success { background: rgba(52,211,153,0.22); color: #6ee7b7; }
    .sol-tag.info { background: rgba(56,189,248,0.2); color: #7dd3fc; }
</style>

<div class="flex-between" style="margin-bottom: 16px;">
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <h1 style="margin:0;font-size:20px;color:#fff;">Giám sát Real-time &amp; Biểu đồ hiệu năng</h1>
        <span id="live-pill" class="live-pill"><span class="pulse"></span><span id="live-pill-text">LIVE</span></span>
        <span id="last-sync" class="muted" style="font-size:12px;">Đang kết nối...</span>
    </div>
    <div class="filters" style="margin:0;">
        <select id="sel-window">
            <option value="1m">1 phút gần nhất</option>
            <option value="5m" selected>5 phút gần nhất</option>
            <option value="15m">15 phút gần nhất</option>
            <option value="1h">1 giờ gần nhất</option>
        </select>
        <select id="sel-interval">
            <option value="1000">Cập nhật: 1s</option>
            <option value="2000" selected>Cập nhật: 2s</option>
            <option value="5000">Cập nhật: 5s</option>
        </select>
        <button type="button" id="btn-toggle-live" class="btn">Tạm dừng</button>
        <button type="button" id="btn-refresh-now" class="btn btn-primary">Làm mới ngay</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi">
        <div class="label">Lưu lượng (QPS)</div>
        <div class="value" id="kpi-qps">0.00</div>
        <div class="sub"><span id="kpi-total">0</span> truy vấn / <span id="kpi-reqs">0</span> requests</div>
    </div>
    <div class="kpi" id="kpi-slow-card">
        <div class="label">Truy vấn chậm (&ge; <span id="kpi-threshold">{{ $slowThresholdMs }}</span>ms)</div>
        <div class="value" id="kpi-slow">0</div>
        <div class="sub" id="kpi-slow-pct">0% tổng truy vấn</div>
    </div>
    <div class="kpi">
        <div class="label">Thời gian TB / Max</div>
        <div class="value"><span id="kpi-avg">0.00</span> ms</div>
        <div class="sub">Max: <strong id="kpi-max">0.00</strong> ms &bull; Tổng: <span id="kpi-sum">0.00</span> s</div>
    </div>
    <div class="kpi" id="kpi-n1-card">
        <div class="label">Điểm nóng N+1</div>
        <div class="value" id="kpi-n1">0</div>
        <div class="sub">Trạng thái ghi: <strong id="kpi-rec">{{ $recordingEnabled ? 'Đang bật' : 'Tạm dừng' }}</strong></div>
    </div>
</div>

{{-- Real-Time Query River Flow Visualization --}}
<div class="card">
    <div class="flex-between" style="margin-bottom:10px;">
        <div>
            <h2 style="margin:0;">Dòng chảy truy vấn Real-time (Query River Flow &amp; Điểm nghẽn)</h2>
            <div class="muted" style="font-size:12px;">
                Dòng chảy từ <strong>Trang / Endpoint</strong> &rarr; <strong>Bảng MySQL</strong>. Nhánh càng rộng = tải càng lớn; màu đỏ chảy chậm = <strong>Điểm nghẽn (Bottleneck)</strong>. Nhấn vào nút hoặc nhánh sông để lọc phân tích bên dưới.
            </div>
        </div>
        <div class="legend">
            <span class="legend-item"><span class="legend-swatch" style="background:#38bdf8;"></span>Khỏe (chảy nhanh)</span>
            <span class="legend-item"><span class="legend-swatch" style="background:#fbbf24;"></span>Cảnh báo / N+1</span>
            <span class="legend-item"><span class="legend-swatch" style="background:#f87171;"></span>Điểm nghẽn (ùn tắc)</span>
        </div>
    </div>
    <div class="river-headers">
        <span>Nguồn Request / Trang (Endpoints)</span>
        <span>Luồng truy vấn SQL (Độ rộng = Tổng ms)</span>
        <span>Bảng Database đích (MySQL Tables)</span>
    </div>
    <div class="river-stage">
        <canvas id="river-canvas"></canvas>
        <div id="tip-river" class="chart-tooltip"></div>
    </div>
</div>

{{-- Automatic Bottleneck Diagnosis & Action Plan --}}
<div class="card">
    <div class="flex-between" style="margin-bottom:12px;">
        <div>
            <h2 style="margin:0;">Phân tích Điểm nghẽn &amp; Phương án giải quyết đề xuất</h2>
            <div class="muted" style="font-size:12px;" id="bn-filter-label">Hiển thị các điểm nghẽn có mức ảnh hưởng cao nhất trong khung thời gian hiện tại</div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <button type="button" id="btn-clear-river-filter" class="btn btn-sm" style="display:none;" onclick="clearRiverFilter()">Bỏ lọc dòng sông &times;</button>
            <a href="{{ route('query-monitor.tuning') }}" class="btn btn-primary btn-sm">Mở SQL Tuning Benchmark &rarr;</a>
        </div>
    </div>
    <div id="bottlenecks-container" class="bn-grid">
        <div class="muted">Đang phân tích luồng truy vấn...</div>
    </div>
</div>

<div class="charts-grid">
    <div class="card" style="margin-bottom:0;">
        <div class="flex-between" style="margin-bottom:10px;">
            <h2 style="margin:0;">Lưu lượng truy vấn theo thời gian</h2>
            <div class="legend">
                <span class="legend-item"><span class="legend-swatch" style="background:#38bdf8;"></span>Bình thường</span>
                <span class="legend-item"><span class="legend-swatch" style="background:#f87171;"></span>Chậm (Slow)</span>
            </div>
        </div>
        <div class="chart-box">
            <canvas id="chart-throughput"></canvas>
            <div id="tip-throughput" class="chart-tooltip"></div>
        </div>
    </div>

    <div class="card" style="margin-bottom:0;">
        <div class="flex-between" style="margin-bottom:10px;">
            <h2 style="margin:0;">Độ trễ truy vấn — TB &amp; Max (ms)</h2>
            <div class="legend">
                <span class="legend-item"><span class="legend-swatch" style="background:#34d399;"></span>TB (ms)</span>
                <span class="legend-item"><span class="legend-swatch" style="background:#fbbf24;"></span>Max (ms)</span>
                <span class="legend-item"><span class="legend-swatch" style="background:#f87171;"></span>Ngưỡng Slow</span>
            </div>
        </div>
        <div class="chart-box">
            <canvas id="chart-latency"></canvas>
            <div id="tip-latency" class="chart-tooltip"></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="flex-between" style="margin-bottom:12px;">
        <h2 style="margin:0;">Phân bổ theo loại câu lệnh SQL</h2>
        <span class="muted" style="font-size:12px;">Tự động cập nhật theo khung thời gian đã chọn</span>
    </div>
    <div class="type-grid">
        <div style="width:170px;height:170px;margin:0 auto;">
            <canvas id="chart-types" style="width:170px;height:170px;"></canvas>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Loại SQL</th>
                        <th class="num">Số lần chạy</th>
                        <th class="num">Tỷ lệ</th>
                        <th class="num">TB (ms)</th>
                        <th class="num">Tổng (ms)</th>
                    </tr>
                </thead>
                <tbody id="types-tbody">
                    <tr><td colspan="5" class="muted">Chưa có dữ liệu...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="flex-between" style="margin-bottom:12px;">
        <div>
            <h2 style="margin:0;">Luồng truy vấn trực tiếp (Real-time Stream)</h2>
            <div class="muted" style="font-size:12px;">Nhấn vào bất kỳ dòng nào để mở bảng <strong>Chi tiết truy vấn, SQL hoàn chỉnh, EXPLAIN &amp; Chuỗi truy vấn cùng Request</strong></div>
        </div>
        <div class="filters" style="margin:0;">
            <input type="text" id="filter-q" placeholder="Lọc nhanh SQL / URI / Caller..." style="min-width:210px;">
            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;">
                <input type="checkbox" id="filter-slow"> Chỉ Slow
            </label>
            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;">
                <input type="checkbox" id="filter-n1"> Chỉ N+1
            </label>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID / Thời gian</th>
                    <th>Loại</th>
                    <th>Câu lệnh SQL</th>
                    <th>Endpoint / Caller</th>
                    <th class="num">Thời gian</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="stream-tbody">
                <tr><td colspan="6" class="muted" style="text-align:center;padding:24px;">Đang tải dữ liệu real-time...</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Slide-over Detail Inspector Drawer --}}
<div id="detail-backdrop" class="drawer-backdrop" onclick="if(event.target===this) closeDrawer()">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <div style="font-weight:700;font-size:16px;color:#fff;" id="drawer-title">Chi tiết truy vấn</div>
                <div class="muted" style="font-size:12px;" id="drawer-subtitle"></div>
            </div>
            <button type="button" class="btn btn-sm" onclick="closeDrawer()">Đóng &times;</button>
        </div>
        <div class="drawer-body" id="drawer-body">
            <div class="muted">Đang tải chi tiết...</div>
        </div>
    </div>
</div>

<script>
(function() {
    const LIVE_API = @json(route('query-monitor.api.live'));
    const QUERY_DETAIL_BASE = @json(url(config('query-monitor.route_prefix', 'query-monitor') . '/api/query'));
    const EXPLAIN_BASE = @json(url(config('query-monitor.route_prefix', 'query-monitor') . '/explain'));
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    let isPaused = false;
    let timer = null;
    let inFlight = false;
    let seenMaxId = 0;
    let firstLoad = true;
    let selectedQueryId = null;
    let lastData = null;

    const TYPE_COLORS = {
        SELECT: '#38bdf8',
        INSERT: '#34d399',
        UPDATE: '#fbbf24',
        DELETE: '#f87171',
        OTHER: '#94a3b8'
    };

    const $ = id => document.getElementById(id);

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }

    function fmtNum(n, dec = 0) {
        return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec });
    }

    // ---------------------------------------------------------
    // Polling Loop
    // ---------------------------------------------------------
    async function fetchLive() {
        if (inFlight) return;
        inFlight = true;
        try {
            const params = new URLSearchParams({
                window: $('sel-window').value,
                slow_only: $('filter-slow').checked ? '1' : '0',
                n1_only: $('filter-n1').checked ? '1' : '0',
                q: $('filter-q').value.trim(),
            });
            const res = await fetch(`${LIVE_API}?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            lastData = data;
            renderAll(data);
            $('last-sync').textContent = `Cập nhật lúc ${data.server_time} (${data.timezone || 'GMT+7'})`;
        } catch (err) {
            $('last-sync').textContent = `Lỗi kết nối: ${err.message}`;
        } finally {
            inFlight = false;
        }
    }

    function scheduleNext() {
        if (timer) clearInterval(timer);
        if (!isPaused) {
            timer = setInterval(fetchLive, Number($('sel-interval').value));
        }
    }

    $('btn-toggle-live').addEventListener('click', () => {
        isPaused = !isPaused;
        $('btn-toggle-live').textContent = isPaused ? 'Tiếp tục Live' : 'Tạm dừng';
        $('live-pill').classList.toggle('paused', isPaused);
        $('live-pill-text').textContent = isPaused ? 'PAUSED' : 'LIVE';
        scheduleNext();
        if (!isPaused) fetchLive();
    });
    $('btn-refresh-now').addEventListener('click', fetchLive);
    $('sel-window').addEventListener('change', fetchLive);
    $('sel-interval').addEventListener('change', scheduleNext);
    $('filter-slow').addEventListener('change', fetchLive);
    $('filter-n1').addEventListener('change', fetchLive);
    let searchDebounce = null;
    $('filter-q').addEventListener('input', () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(fetchLive, 250);
    });

    // ---------------------------------------------------------
    // Render KPIs, Charts & Stream Table
    // ---------------------------------------------------------
    function renderAll(data) {
        const s = data.stats;
        $('kpi-qps').textContent = fmtNum(s.qps, 2);
        $('kpi-total').textContent = fmtNum(s.total_queries);
        $('kpi-reqs').textContent = fmtNum(s.total_requests);
        $('kpi-threshold').textContent = data.slow_threshold_ms;
        $('kpi-slow').textContent = fmtNum(s.slow_queries);
        const pct = s.total_queries > 0 ? ((s.slow_queries * 100) / s.total_queries).toFixed(2) : '0';
        $('kpi-slow-pct').textContent = `${pct}% tổng truy vấn`;
        $('kpi-slow-card').className = 'kpi ' + (s.slow_queries > 0 ? 'danger' : 'success');
        $('kpi-avg').textContent = fmtNum(s.avg_ms, 2);
        $('kpi-max').textContent = fmtNum(s.max_ms, 2);
        $('kpi-sum').textContent = fmtNum(s.total_ms / 1000, 2);
        $('kpi-n1').textContent = fmtNum(s.n1_hotspots);
        $('kpi-n1-card').className = 'kpi ' + (s.n1_hotspots > 0 ? 'warning' : 'success');
        $('kpi-rec').textContent = data.recording_enabled ? 'Đang bật' : 'Tạm dừng';

        drawThroughputChart(data.series);
        drawLatencyChart(data.series, data.slow_threshold_ms);
        drawTypesChart(data.types);
        updateRiverData(data.river);
        renderBottlenecks(data.river);
        renderStreamTable(data.recent);
    }

    // ---------------------------------------------------------
    // Real-Time River Flow Engine (60fps Animated Bézier Streams)
    // ---------------------------------------------------------
    const riverState = {
        data: null,
        canvasInfo: null,
        nodesLeft: [],
        nodesRight: [],
        branches: [],
        particles: [],
        hover: null, // { type: 'source'|'target'|'link', item }
        filter: null, // { type: 'source'|'target', id: string }
    };

    const HEALTH_COLOR = {
        ok: { stroke: '#38bdf8', glow: 'rgba(56,189,248,0.22)', particle: '#7dd3fc', speed: 0.0085 },
        warn: { stroke: '#fbbf24', glow: 'rgba(251,191,36,0.26)', particle: '#fde68a', speed: 0.0045 },
        bottleneck: { stroke: '#f87171', glow: 'rgba(248,113,113,0.34)', particle: '#fecaca', speed: 0.0022 },
    };

    function updateRiverData(river) {
        riverState.data = river || { sources: [], targets: [], links: [], bottlenecks: [] };
        layoutRiver();
    }

    function layoutRiver() {
        const canvas = $('river-canvas');
        if (!canvas || !riverState.data) return;
        // Size canvas only on layout/resize (eliminates 60fps forced reflows inside drawRiverFrame)
        riverState.canvasInfo = prepCanvas(canvas);
        const w = Math.max(300, riverState.canvasInfo.w);
        const h = Math.max(260, riverState.canvasInfo.h);
        const padX = 16, padY = 18;
        const nodeW = Math.min(230, Math.max(135, w * 0.22));
        const sources = riverState.data.sources || [];
        const targets = riverState.data.targets || [];
        const links = riverState.data.links || [];

        function buildColumn(items, x) {
            if (!items.length) return [];
            const gap = 8;
            const availH = h - padY * 2 - gap * Math.max(0, items.length - 1);
            const totalMs = Math.max(1, items.reduce((a, b) => a + Number(b.total_ms || 0), 0));
            const minH = 30;
            const heights = items.map(it => Math.max(minH, (Number(it.total_ms || 0) / totalMs) * availH));
            const sumH = heights.reduce((a, b) => a + b, 0);
            const scale = sumH > availH ? (availH / sumH) : 1;

            let y = padY;
            return items.map((it, i) => {
                const nh = Math.max(24, heights[i] * scale);
                const node = { ...it, x, y, w: nodeW, h: nh, cy: y + nh / 2 };
                y += nh + gap;
                return node;
            });
        }

        riverState.nodesLeft = buildColumn(sources, padX);
        riverState.nodesRight = buildColumn(targets, w - padX - nodeW);

        const leftById = Object.fromEntries(riverState.nodesLeft.map(n => [n.id, n]));
        const rightById = Object.fromEntries(riverState.nodesRight.map(n => [n.id, n]));
        const maxLinkMs = Math.max(1, ...links.map(l => Number(l.total_ms || 0)));

        riverState.branches = links.map(l => {
            const s = leftById[l.source];
            const t = rightById[l.target];
            if (!s || !t) return null;
            const width = Math.max(3.5, Math.min(26, (Number(l.total_ms || 0) / maxLinkMs) * 24));
            return {
                ...l,
                x1: s.x + s.w,
                y1: s.cy,
                x2: t.x,
                y2: t.cy,
                cx1: s.x + s.w + (t.x - (s.x + s.w)) * 0.42,
                cx2: t.x - (t.x - (s.x + s.w)) * 0.42,
                width,
            };
        }).filter(Boolean);

        // Seed or reconcile particles along branches
        const desired = [];
        riverState.branches.forEach((b, bIdx) => {
            const count = Math.max(2, Math.min(8, Math.ceil(b.exec_count / 3) + 1));
            for (let i = 0; i < count; i++) {
                desired.push({
                    bIdx,
                    t: (i / count) + Math.random() * 0.12,
                    r: Math.max(2.2, Math.min(4.6, b.width * 0.24)),
                    offset: (Math.random() - 0.5) * Math.max(1, b.width * 0.45),
                });
            }
        });
        if (riverState.particles.length !== desired.length) {
            riverState.particles = desired;
        }
    }

    function cubicPoint(b, t) {
        const u = 1 - t;
        const x = u*u*u*b.x1 + 3*u*u*t*b.cx1 + 3*u*t*t*b.cx2 + t*t*t*b.x2;
        const y = u*u*u*b.y1 + 3*u*u*t*b.y1 + 3*u*t*t*b.y2 + t*t*t*b.y2;
        return { x, y };
    }

    function drawRiverFrame(nowMs) {
        const canvas = $('river-canvas');
        if (canvas) {
            if (!riverState.canvasInfo) {
                riverState.canvasInfo = prepCanvas(canvas);
            }
            const { ctx, w, h } = riverState.canvasInfo;
            ctx.clearRect(0, 0, w, h);

            if (!riverState.branches.length && !riverState.nodesLeft.length) {
                ctx.fillStyle = '#94a3b8';
                ctx.font = '13px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Chưa có dòng chảy truy vấn nào trong khung thời gian này. Hãy mở trang trong hệ thống chính để thấy dòng sông SQL chạy real-time!', w / 2, h / 2);
            } else {
                const pulse = (Math.sin(nowMs / 320) + 1) / 2;
                const activeFilter = riverState.filter;

                // 1. Draw River Branches (Bézier ribbons)
                riverState.branches.forEach((b, idx) => {
                    const hc = HEALTH_COLOR[b.health] || HEALTH_COLOR.ok;
                    const dimmed = activeFilter && (
                        (activeFilter.type === 'source' && b.source !== activeFilter.id) ||
                        (activeFilter.type === 'target' && b.target !== activeFilter.id)
                    );
                    const isHover = riverState.hover && (
                        (riverState.hover.type === 'link' && riverState.hover.item === b) ||
                        (riverState.hover.type === 'source' && b.source === riverState.hover.item.id) ||
                        (riverState.hover.type === 'target' && b.target === riverState.hover.item.id)
                    );

                    ctx.save();
                    ctx.globalAlpha = dimmed ? 0.14 : (isHover ? 0.95 : 0.62);
                    ctx.beginPath();
                    ctx.moveTo(b.x1, b.y1);
                    ctx.bezierCurveTo(b.cx1, b.y1, b.cx2, b.y2, b.x2, b.y2);

                    const grad = ctx.createLinearGradient(b.x1, b.y1, b.x2, b.y2);
                    grad.addColorStop(0, hc.glow);
                    grad.addColorStop(0.5, hc.stroke);
                    grad.addColorStop(1, hc.glow);
                    ctx.strokeStyle = grad;
                    ctx.lineWidth = isHover ? b.width + 2.5 : b.width;
                    ctx.lineCap = 'round';
                    ctx.stroke();

                    // Pulsing Bottleneck Dam ring at midpoint for congested branches
                    if (b.health === 'bottleneck' && !dimmed) {
                        const mid = cubicPoint(b, 0.5);
                        ctx.beginPath();
                        ctx.arc(mid.x, mid.y, 7 + pulse * 8, 0, Math.PI * 2);
                        ctx.strokeStyle = `rgba(248,113,113,${0.75 - pulse * 0.55})`;
                        ctx.lineWidth = 2;
                        ctx.stroke();

                        ctx.beginPath();
                        ctx.arc(mid.x, mid.y, 4.5, 0, Math.PI * 2);
                        ctx.fillStyle = '#f87171';
                        ctx.fill();
                    }
                    ctx.restore();
                });

                // 2. Animate glowing river particles
                riverState.particles.forEach(p => {
                    const b = riverState.branches[p.bIdx];
                    if (!b) return;
                    const hc = HEALTH_COLOR[b.health] || HEALTH_COLOR.ok;
                    if (!isPaused) {
                        p.t += hc.speed;
                        if (p.t > 1) p.t -= 1;
                    }
                    const dimmed = activeFilter && (
                        (activeFilter.type === 'source' && b.source !== activeFilter.id) ||
                        (activeFilter.type === 'target' && b.target !== activeFilter.id)
                    );
                    if (dimmed) return;

                    const pt = cubicPoint(b, p.t);
                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(pt.x, pt.y + p.offset, p.r, 0, Math.PI * 2);
                    ctx.fillStyle = hc.particle;
                    ctx.shadowColor = hc.stroke;
                    ctx.shadowBlur = 8;
                    ctx.fill();
                    ctx.restore();
                });

                // 3. Draw Left (Endpoints) & Right (DB Tables) nodes
                function drawNode(n, side) {
                    const hc = HEALTH_COLOR[n.health] || HEALTH_COLOR.ok;
                    const isSelected = activeFilter && activeFilter.type === side && activeFilter.id === n.id;
                    const isHovered = riverState.hover && riverState.hover.type === side && riverState.hover.item === n;

                    ctx.save();
                    ctx.fillStyle = isSelected || isHovered ? '#1e293b' : '#131d31';
                    ctx.strokeStyle = isSelected || isHovered ? '#fff' : hc.stroke;
                    ctx.lineWidth = isSelected || isHovered ? 2 : 1.3;

                    const r = 7;
                    ctx.beginPath();
                    ctx.moveTo(n.x + r, n.y);
                    ctx.lineTo(n.x + n.w - r, n.y);
                    ctx.quadraticCurveTo(n.x + n.w, n.y, n.x + n.w, n.y + r);
                    ctx.lineTo(n.x + n.w, n.y + n.h - r);
                    ctx.quadraticCurveTo(n.x + n.w, n.y + n.h, n.x + n.w - r, n.y + n.h);
                    ctx.lineTo(n.x + r, n.y + n.h);
                    ctx.quadraticCurveTo(n.x, n.y + n.h, n.x, n.y + n.h - r);
                    ctx.lineTo(n.x, n.y + r);
                    ctx.quadraticCurveTo(n.x, n.y, n.x + r, n.y);
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();

                    // Health accent strip
                    ctx.fillStyle = hc.stroke;
                    ctx.fillRect(side === 'source' ? n.x : n.x + n.w - 4, n.y + 4, 4, Math.max(8, n.h - 8));

                    // Label
                    ctx.fillStyle = '#f8fafc';
                    ctx.font = '600 12px sans-serif';
                    ctx.textAlign = 'left';
                    const maxChars = Math.floor((n.w - 22) / 6.8);
                    const lbl = n.label.length > maxChars ? n.label.slice(0, maxChars - 1) + '…' : n.label;
                    ctx.fillText(lbl, n.x + 11, n.y + Math.min(18, n.h * 0.48));

                    if (n.h >= 34) {
                        ctx.fillStyle = '#94a3b8';
                        ctx.font = '11px sans-serif';
                        const sub = `${fmtNum(n.total_ms, 1)}ms • ${n.exec_count}q` + (n.slow_count > 0 ? ` • ${n.slow_count} slow` : '');
                        ctx.fillText(sub, n.x + 11, n.y + Math.min(33, n.h - 7));
                    }
                    ctx.restore();
                }

                riverState.nodesLeft.forEach(n => drawNode(n, 'source'));
                riverState.nodesRight.forEach(n => drawNode(n, 'target'));
            }
        }
        requestAnimationFrame(drawRiverFrame);
    }

    function hitTestRiver(x, y) {
        for (const n of riverState.nodesLeft) {
            if (x >= n.x && x <= n.x + n.w && y >= n.y && y <= n.y + n.h) return { type: 'source', item: n };
        }
        for (const n of riverState.nodesRight) {
            if (x >= n.x && x <= n.x + n.w && y >= n.y && y <= n.y + n.h) return { type: 'target', item: n };
        }
        for (const b of riverState.branches) {
            for (let t = 0.08; t <= 0.92; t += 0.06) {
                const pt = cubicPoint(b, t);
                const dist = Math.hypot(x - pt.x, y - pt.y);
                if (dist <= Math.max(8, b.width * 0.65)) return { type: 'link', item: b };
            }
        }
        return null;
    }

    const riverCanvas = $('river-canvas');
    const riverTip = $('tip-river');
    riverCanvas.addEventListener('mousemove', e => {
        const rect = riverCanvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const hit = hitTestRiver(x, y);
        riverState.hover = hit;
        if (!hit) {
            riverTip.style.display = 'none';
            return;
        }
        if (hit.type === 'source' || hit.type === 'target') {
            const n = hit.item;
            const kind = hit.type === 'source' ? 'Trang / Endpoint' : 'Bảng MySQL';
            riverTip.innerHTML = `<div class="muted" style="font-size:10px;">${kind} (nhấn để lọc)</div><strong>${esc(n.label)}</strong><br>Tổng tải: <strong>${fmtNum(n.total_ms, 1)} ms</strong> &bull; ${n.exec_count} queries<br>Slow: <span style="color:#f87171">${n.slow_count}</span> &bull; N+1: <span style="color:#fbbf24">${n.n1_count}</span>`;
        } else {
            const b = hit.item;
            riverTip.innerHTML = `<strong>${esc(b.source)} &rarr; ${esc(b.target)}</strong><br>Tổng: <strong>${fmtNum(b.total_ms, 1)} ms</strong> (${b.exec_count} queries)<br>TB: ${fmtNum(b.avg_ms, 2)} ms &bull; Max: ${fmtNum(b.max_ms, 2)} ms<br>Slow: <span style="color:#f87171">${b.slow_count}</span> &bull; Max N+1: &times;${b.max_dup}<br><span class="muted" style="font-size:11px;">Nhấn để soi chi tiết truy vấn nặng nhất</span>`;
        }
        riverTip.style.display = 'block';
        riverTip.style.left = Math.min(rect.width - riverTip.offsetWidth - 10, Math.max(10, x + 14)) + 'px';
        riverTip.style.top = Math.min(rect.height - riverTip.offsetHeight - 10, Math.max(10, y - 10)) + 'px';
    });
    riverCanvas.addEventListener('mouseleave', () => {
        riverState.hover = null;
        riverTip.style.display = 'none';
    });
    riverCanvas.addEventListener('click', e => {
        const rect = riverCanvas.getBoundingClientRect();
        const hit = hitTestRiver(e.clientX - rect.left, e.clientY - rect.top);
        if (!hit) {
            clearRiverFilter();
            return;
        }
        if (hit.type === 'link') {
            openQueryDetail(hit.item.top_sample_id);
            return;
        }
        riverState.filter = { type: hit.type, id: hit.item.id };
        renderBottlenecks(riverState.data);
    });

    window.clearRiverFilter = function() {
        riverState.filter = null;
        renderBottlenecks(riverState.data);
    };

    function renderBottlenecks(river) {
        const box = $('bottlenecks-container');
        const btnClear = $('btn-clear-river-filter');
        const label = $('bn-filter-label');
        let list = (river && river.bottlenecks) ? river.bottlenecks : [];

        if (riverState.filter) {
            btnClear.style.display = 'inline-flex';
            if (riverState.filter.type === 'source') {
                list = list.filter(b => b.uri === riverState.filter.id);
                label.innerHTML = `Đang lọc điểm nghẽn theo Trang: <strong>${esc(riverState.filter.id)}</strong>`;
            } else {
                list = list.filter(b => b.table === riverState.filter.id);
                label.innerHTML = `Đang lọc điểm nghẽn theo Bảng MySQL: <strong>${esc(riverState.filter.id)}</strong>`;
            }
        } else {
            btnClear.style.display = 'none';
            label.textContent = 'Hiển thị các điểm nghẽn có mức ảnh hưởng cao nhất trong khung thời gian hiện tại';
        }

        if (!list.length) {
            box.innerHTML = '<div class="muted" style="padding:12px 0;">Chưa phát hiện điểm nghẽn nào trong phạm vi đang xem.</div>';
            return;
        }

        box.innerHTML = list.slice(0, 6).map(b => {
            const issueBadges = (b.issues || []).map(i =>
                `<span class="badge ${i.severity === 'critical' ? 'badge-slow' : 'badge-n1'}">${esc(i.label)}</span>`
            ).join(' ');

            const solHtml = (b.solutions || []).map(s =>
                `<div class="sol-item"><span class="sol-tag ${esc(s.color)}">${esc(s.tag)}</span>${esc(s.text)}</div>`
            ).join('');

            return `<div class="bn-card health-${esc(b.health)}">
                <div class="flex-between" style="margin-bottom:6px;">
                    <div style="font-size:13px;font-weight:700;color:#fff;">
                        <span>${esc(b.uri)}</span> <span class="muted">&rarr;</span> <code style="color:#38bdf8;">${esc(b.table)}</code>
                    </div>
                    <span class="badge ${b.health === 'bottleneck' ? 'badge-slow' : (b.health === 'warn' ? 'badge-n1' : 'badge-ok')}">
                        Chiếm ${b.share_pct}% tải DB
                    </span>
                </div>
                <div class="muted" style="font-size:12px;margin-bottom:6px;">
                    Tổng: <strong style="color:#fff;">${fmtNum(b.total_ms, 1)} ms</strong> &bull;
                    TB: ${fmtNum(b.avg_ms, 2)} ms &bull; Max: ${fmtNum(b.max_ms, 2)} ms &bull;
                    ${b.exec_count} lượt &bull; Caller: <code>${esc(b.caller)}</code>
                </div>
                <div style="display:flex;gap:5px;flex-wrap:wrap;margin-bottom:8px;">
                    <span class="badge badge-${esc(b.query_type.toLowerCase())}">${esc(b.query_type)}</span>
                    ${issueBadges || '<span class="badge badge-ok">Ổn định</span>'}
                </div>
                <div class="sql-code" style="max-width:none;font-size:11.5px;">${esc(b.sql_short)}</div>
                ${solHtml}
                <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openQueryDetail(${Number(b.sample_id)})">Soi chi tiết &amp; EXPLAIN</button>
                    <a class="btn btn-sm" href="{{ url(config('query-monitor.route_prefix', 'query-monitor') . '/fingerprint') }}/${encodeURIComponent(b.sql_hash)}">Phân tích sâu &rarr;</a>
                </div>
            </div>`;
        }).join('');
    }

    function prepCanvas(canvas) {
        const rect = canvas.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        canvas.width = Math.max(1, Math.floor(rect.width * dpr));
        canvas.height = Math.max(1, Math.floor(rect.height * dpr));
        const ctx = canvas.getContext('2d');
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        return { ctx, w: rect.width, h: rect.height };
    }

    // Chart 1: Stacked Bar Chart (Throughput + Slow)
    function drawThroughputChart(series) {
        const canvas = $('chart-throughput');
        const { ctx, w, h } = prepCanvas(canvas);
        const pad = { top: 14, right: 14, bottom: 28, left: 42 };
        const plotW = Math.max(10, w - pad.left - pad.right);
        const plotH = Math.max(10, h - pad.top - pad.bottom);

        ctx.clearRect(0, 0, w, h);
        const maxVal = Math.max(5, ...series.map(d => d.total));

        // Gridlines + Y labels
        ctx.strokeStyle = 'rgba(148,163,184,0.14)';
        ctx.fillStyle = '#94a3b8';
        ctx.font = '11px sans-serif';
        ctx.textAlign = 'right';
        for (let i = 0; i <= 4; i++) {
            const val = (maxVal * i) / 4;
            const y = pad.top + plotH - (plotH * i) / 4;
            ctx.beginPath();
            ctx.moveTo(pad.left, y);
            ctx.lineTo(w - pad.right, y);
            ctx.stroke();
            ctx.fillText(Math.round(val), pad.left - 6, y + 4);
        }

        const n = series.length || 1;
        const step = plotW / n;
        const barW = Math.max(3, step * 0.68);

        series.forEach((d, idx) => {
            const x = pad.left + idx * step + (step - barW) / 2;
            const normal = Math.max(0, d.total - d.slow);
            const normalH = (normal / maxVal) * plotH;
            const slowH = (d.slow / maxVal) * plotH;

            if (normalH > 0) {
                ctx.fillStyle = '#38bdf8';
                ctx.fillRect(x, pad.top + plotH - normalH, barW, normalH);
            }
            if (slowH > 0) {
                ctx.fillStyle = '#f87171';
                ctx.fillRect(x, pad.top + plotH - normalH - slowH, barW, slowH);
            }
        });

        // X axis labels (5 ticks)
        ctx.fillStyle = '#94a3b8';
        ctx.textAlign = 'center';
        const ticks = Math.min(5, n);
        for (let i = 0; i < ticks; i++) {
            const idx = Math.min(n - 1, Math.round((i * (n - 1)) / Math.max(1, ticks - 1)));
            const x = pad.left + idx * step + step / 2;
            ctx.fillText(series[idx].label, x, h - 8);
        }

        canvas._meta = { pad, plotW, plotH, series, step };
    }

    // Chart 2: Dual Line Chart (Avg ms & Max ms + Threshold)
    function drawLatencyChart(series, thresholdMs) {
        const canvas = $('chart-latency');
        const { ctx, w, h } = prepCanvas(canvas);
        const pad = { top: 14, right: 14, bottom: 28, left: 48 };
        const plotW = Math.max(10, w - pad.left - pad.right);
        const plotH = Math.max(10, h - pad.top - pad.bottom);

        ctx.clearRect(0, 0, w, h);
        const maxVal = Math.max(thresholdMs * 1.15, 10, ...series.map(d => Math.max(d.max_ms, d.avg_ms)));

        ctx.strokeStyle = 'rgba(148,163,184,0.14)';
        ctx.fillStyle = '#94a3b8';
        ctx.font = '11px sans-serif';
        ctx.textAlign = 'right';
        for (let i = 0; i <= 4; i++) {
            const val = (maxVal * i) / 4;
            const y = pad.top + plotH - (plotH * i) / 4;
            ctx.beginPath();
            ctx.moveTo(pad.left, y);
            ctx.lineTo(w - pad.right, y);
            ctx.stroke();
            ctx.fillText(Math.round(val) + 'ms', pad.left - 6, y + 4);
        }

        // Slow threshold dashed line
        const thY = pad.top + plotH - (thresholdMs / maxVal) * plotH;
        ctx.save();
        ctx.setLineDash([5, 4]);
        ctx.strokeStyle = 'rgba(248,113,113,0.75)';
        ctx.lineWidth = 1.2;
        ctx.beginPath();
        ctx.moveTo(pad.left, thY);
        ctx.lineTo(w - pad.right, thY);
        ctx.stroke();
        ctx.restore();

        const n = series.length || 1;
        const step = plotW / Math.max(1, n - 1);

        function drawLine(key, stroke, fill) {
            if (series.length < 2) return;
            ctx.beginPath();
            series.forEach((d, idx) => {
                const x = pad.left + idx * step;
                const y = pad.top + plotH - (d[key] / maxVal) * plotH;
                if (idx === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
            });
            ctx.strokeStyle = stroke;
            ctx.lineWidth = 2;
            ctx.stroke();

            // Soft area fill
            ctx.lineTo(pad.left + (series.length - 1) * step, pad.top + plotH);
            ctx.lineTo(pad.left, pad.top + plotH);
            ctx.closePath();
            ctx.fillStyle = fill;
            ctx.fill();
        }

        drawLine('max_ms', '#fbbf24', 'rgba(251,191,36,0.10)');
        drawLine('avg_ms', '#34d399', 'rgba(52,211,153,0.16)');

        // X axis labels
        ctx.fillStyle = '#94a3b8';
        ctx.textAlign = 'center';
        const ticks = Math.min(5, n);
        for (let i = 0; i < ticks; i++) {
            const idx = Math.min(n - 1, Math.round((i * (n - 1)) / Math.max(1, ticks - 1)));
            const x = pad.left + idx * step;
            ctx.fillText(series[idx].label, x, h - 8);
        }

        canvas._meta = { pad, plotW, plotH, series, step };
    }

    // Chart 3: Donut Chart (Query Types)
    function drawTypesChart(types) {
        const canvas = $('chart-types');
        const { ctx, w, h } = prepCanvas(canvas);
        ctx.clearRect(0, 0, w, h);

        const total = types.reduce((acc, t) => acc + Number(t.cnt || 0), 0);
        const cx = w / 2, cy = h / 2, r = Math.min(w, h) / 2 - 8, inner = r * 0.6;

        if (total === 0) {
            ctx.beginPath();
            ctx.arc(cx, cy, r, 0, Math.PI * 2);
            ctx.strokeStyle = '#334155';
            ctx.lineWidth = r - inner;
            ctx.stroke();
            $('types-tbody').innerHTML = '<tr><td colspan="5" class="muted">Chưa có truy vấn trong khung thời gian này.</td></tr>';
            return;
        }

        let start = -Math.PI / 2;
        types.forEach(t => {
            const slice = (Number(t.cnt) / total) * Math.PI * 2;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, start, start + slice);
            ctx.closePath();
            ctx.fillStyle = TYPE_COLORS[t.query_type] || TYPE_COLORS.OTHER;
            ctx.fill();
            start += slice;
        });

        // Inner cutout
        ctx.beginPath();
        ctx.arc(cx, cy, inner, 0, Math.PI * 2);
        ctx.fillStyle = '#1e293b';
        ctx.fill();

        ctx.fillStyle = '#fff';
        ctx.font = 'bold 16px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(fmtNum(total), cx, cy + 4);
        ctx.fillStyle = '#94a3b8';
        ctx.font = '11px sans-serif';
        ctx.fillText('queries', cx, cy + 19);

        $('types-tbody').innerHTML = types.map(t => {
            const color = TYPE_COLORS[t.query_type] || TYPE_COLORS.OTHER;
            const pct = ((Number(t.cnt) * 100) / total).toFixed(1);
            return `<tr>
                <td><span class="legend-swatch" style="background:${color};margin-right:6px;"></span><strong>${esc(t.query_type)}</strong></td>
                <td class="num">${fmtNum(t.cnt)}</td>
                <td class="num">${pct}%</td>
                <td class="num">${fmtNum(t.avg_ms, 2)}</td>
                <td class="num"><strong>${fmtNum(t.total_ms, 1)}</strong></td>
            </tr>`;
        }).join('');
    }

    // Hover tooltips for charts
    function attachTooltip(canvasId, tipId, formatter) {
        const canvas = $(canvasId);
        const tip = $(tipId);
        canvas.addEventListener('mousemove', e => {
            const m = canvas._meta;
            if (!m || !m.series || !m.series.length) return;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            if (x < m.pad.left || x > rect.width - m.pad.right) {
                tip.style.display = 'none';
                return;
            }
            const idx = Math.max(0, Math.min(m.series.length - 1, Math.round((x - m.pad.left) / m.step)));
            const d = m.series[idx];
            tip.innerHTML = formatter(d);
            tip.style.display = 'block';
            const left = Math.min(rect.width - tip.offsetWidth - 8, Math.max(8, x + 12));
            tip.style.left = left + 'px';
            tip.style.top = '12px';
        });
        canvas.addEventListener('mouseleave', () => { tip.style.display = 'none'; });
    }

    attachTooltip('chart-throughput', 'tip-throughput', d =>
        `<strong>${esc(d.label)}</strong><br>Tổng: <strong>${d.total}</strong> query<br>Chậm: <span style="color:#f87171">${d.slow}</span>`
    );
    attachTooltip('chart-latency', 'tip-latency', d =>
        `<strong>${esc(d.label)}</strong><br>TB: <span style="color:#34d399">${fmtNum(d.avg_ms, 2)} ms</span><br>Max: <span style="color:#fbbf24">${fmtNum(d.max_ms, 2)} ms</span><br>Số lượng: ${d.total}`
    );

    // ---------------------------------------------------------
    // Render Live Stream Table
    // ---------------------------------------------------------
    function renderStreamTable(rows) {
        const tbody = $('stream-tbody');
        if (!rows || rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="muted" style="text-align:center;padding:24px;">Chưa có truy vấn nào trong khung thời gian này. Hãy thao tác trên hệ thống chính để thấy dữ liệu chạy real-time!</td></tr>';
            return;
        }

        const prevMax = seenMaxId;
        const newestId = Math.max(...rows.map(r => Number(r.id)));
        if (newestId > seenMaxId) seenMaxId = newestId;

        tbody.innerHTML = rows.map(r => {
            const isNew = !firstLoad && Number(r.id) > prevMax;
            const isSel = Number(r.id) === Number(selectedQueryId);
            const qtype = String(r.query_type || 'OTHER').toLowerCase();
            const sqlShort = r.sql_raw.length > 180 ? r.sql_raw.slice(0, 180) + '...' : r.sql_raw;
            return `<tr class="stream-row ${isNew ? 'is-new' : ''} ${isSel ? 'selected' : ''}" onclick="openQueryDetail(${Number(r.id)})">
                <td style="white-space:nowrap;">
                    <div><strong>#${r.id}</strong></div>
                    <div class="muted" style="font-size:11px;">${esc(r.created_at.slice(11))} GMT+7</div>
                </td>
                <td>
                    <span class="badge badge-${qtype}">${esc(r.query_type)}</span>
                    ${Number(r.is_slow) === 1 ? '<div style="margin-top:3px;"><span class="badge badge-slow">SLOW</span></div>' : ''}
                    ${Number(r.duplicate_in_request) >= 3 ? `<div style="margin-top:3px;"><span class="badge badge-n1">&times;${r.duplicate_in_request}</span></div>` : ''}
                </td>
                <td><div class="sql-code">${esc(sqlShort)}</div></td>
                <td>
                    <div><span class="badge badge-other">${esc(r.http_method)}</span> ${esc((r.uri || '').slice(0, 42))}</div>
                    <div class="muted" style="font-size:11px;margin-top:2px;">${esc(r.caller || '-')}</div>
                </td>
                <td class="num" style="color:${Number(r.is_slow) === 1 ? 'var(--danger)' : 'inherit'}">
                    <strong>${fmtNum(r.time_ms, 2)} ms</strong>
                </td>
                <td><button type="button" class="btn btn-sm" onclick="event.stopPropagation();openQueryDetail(${Number(r.id)})">Chi tiết</button></td>
            </tr>`;
        }).join('');

        firstLoad = false;
    }

    // ---------------------------------------------------------
    // Slide-Over Detail Inspector Drawer
    // ---------------------------------------------------------
    window.openQueryDetail = async function(id) {
        selectedQueryId = id;
        $('detail-backdrop').classList.add('open');
        $('drawer-title').textContent = `Chi tiết truy vấn #${id}`;
        $('drawer-subtitle').textContent = 'Đang tải thông tin...';
        $('drawer-body').innerHTML = '<div class="muted">Đang tải dữ liệu chi tiết...</div>';

        try {
            const res = await fetch(`${QUERY_DETAIL_BASE}/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (!data.ok) {
                $('drawer-body').innerHTML = `<div class="alert alert-danger">${esc(data.error)}</div>`;
                return;
            }
            renderDrawer(data);
        } catch (err) {
            $('drawer-body').innerHTML = `<div class="alert alert-danger">Lỗi tải chi tiết: ${esc(err.message)}</div>`;
        }
    };

    window.closeDrawer = function() {
        $('detail-backdrop').classList.remove('open');
        selectedQueryId = null;
    };

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

    function renderDrawer(data) {
        const q = data.query;
        const req = data.request_summary;
        const qtype = String(q.query_type || 'OTHER').toUpperCase();

        $('drawer-title').innerHTML = `<span class="badge badge-${qtype.toLowerCase()}">${esc(qtype)}</span> Chi tiết truy vấn #${q.id}`;
        $('drawer-subtitle').textContent = `${q.created_at} (GMT+7) • Connection: ${q.connection}`;

        const maxSibMs = Math.max(1, ...req.queries.map(s => Number(s.time_ms || 0)));

        $('drawer-body').innerHTML = `
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="k">Thời gian thực thi</div>
                    <div class="v" style="color:${Number(q.is_slow)===1 ? '#f87171' : '#34d399'};font-size:18px;">${fmtNum(q.time_ms, 2)} ms ${Number(q.is_slow)===1 ? '(SLOW)' : ''}</div>
                </div>
                <div class="meta-item">
                    <div class="k">Lặp trong Request</div>
                    <div class="v" style="color:${Number(q.duplicate_in_request)>=3 ? '#fbbf24' : '#fff'}">&times;${q.duplicate_in_request} ${Number(q.duplicate_in_request)>=3 ? '(N+1)' : ''}</div>
                </div>
                <div class="meta-item">
                    <div class="k">Endpoint</div>
                    <div class="v">${esc(q.http_method)} ${esc(q.uri || 'CLI')}</div>
                </div>
                <div class="meta-item">
                    <div class="k">Vị trí gọi (Caller)</div>
                    <div class="v" style="font-family:monospace;font-size:12px;">${esc(q.caller || 'Unknown')}</div>
                </div>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
                ${qtype === 'SELECT' ? `<button type="button" class="btn btn-primary btn-sm" onclick="runDrawerExplain(${Number(q.id)})">Chạy EXPLAIN phân tích Index</button>` : ''}
                <button type="button" class="btn btn-sm" onclick="copyText('sql-interpolated-box', this)">Copy SQL đầy đủ</button>
                <a class="btn btn-sm" href="${esc(data.fingerprint_url)}">Xem thống kê mẫu truy vấn này &rarr;</a>
            </div>

            <div id="drawer-explain-box" style="display:none;margin-bottom:16px;"></div>

            <div style="margin-bottom:16px;">
                <div class="muted" style="font-size:12px;font-weight:600;margin-bottom:6px;">SQL ĐẦY ĐỦ (ĐÃ GHÉP BINDINGS — CÓ THỂ CHẠY TRỰC TIẾP TRÊN MYSQL)</div>
                <div id="sql-interpolated-box" class="sql-code" style="max-width:none;padding:12px;font-size:12.5px;">${esc(data.sql_interpolated)}</div>
            </div>

            <div style="margin-bottom:16px;">
                <div class="muted" style="font-size:12px;font-weight:600;margin-bottom:6px;">SQL GỐC (PREPARED STATEMENT) &amp; BINDINGS</div>
                <div class="sql-code" style="max-width:none;padding:10px;">${esc(q.sql_raw)}</div>
                <div class="sql-code" style="max-width:none;padding:8px;margin-top:6px;color:#94a3b8;">Bindings: ${esc(JSON.stringify(data.bindings))}</div>
            </div>

            <div>
                <div class="flex-between" style="margin-bottom:8px;">
                    <div class="muted" style="font-size:12px;font-weight:600;">
                        CHUỖI TRUY VẤN CÙNG REQUEST (${req.query_count} queries &bull; Tổng ${fmtNum(req.total_ms, 2)} ms)
                    </div>
                    <span class="muted" style="font-size:11px;">Request ID: ${esc(req.request_id.slice(0, 8))}</span>
                </div>
                <div class="table-wrap" style="border:1px solid #334155;border-radius:8px;">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>SQL</th>
                                <th class="num">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${req.queries.map((s, idx) => {
                                const w = Math.max(4, Math.min(100, (Number(s.time_ms) / maxSibMs) * 100));
                                const cur = Number(s.id) === Number(q.id);
                                return `<tr class="stream-row ${cur ? 'selected' : ''}" onclick="openQueryDetail(${Number(s.id)})">
                                    <td class="muted">${idx + 1}</td>
                                    <td>
                                        <span class="badge badge-${String(s.query_type).toLowerCase()}">${esc(s.query_type)}</span>
                                        <span style="font-family:monospace;font-size:11.5px;margin-left:4px;">${esc(s.sql_raw.slice(0, 95))}${s.sql_raw.length > 95 ? '...' : ''}</span>
                                        <div class="wf-bar-wrap"><div class="wf-bar ${Number(s.is_slow)===1 ? 'slow' : ''}" style="width:${w}%"></div></div>
                                    </td>
                                    <td class="num" style="white-space:nowrap;"><strong>${fmtNum(s.time_ms, 2)} ms</strong></td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    window.copyText = function(elId, btn) {
        const text = $(elId).textContent;
        navigator.clipboard.writeText(text).then(() => {
            const old = btn.textContent;
            btn.textContent = 'Đã copy!';
            setTimeout(() => btn.textContent = old, 1400);
        });
    };

    window.runDrawerExplain = async function(id) {
        const box = $('drawer-explain-box');
        box.style.display = 'block';
        box.innerHTML = '<div class="alert alert-info">Đang chạy EXPLAIN trên MySQL...</div>';
        try {
            const res = await fetch(`${EXPLAIN_BASE}/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.ok) {
                box.innerHTML = `<div class="alert alert-danger">${esc(data.error)}</div>`;
                return;
            }
            let html = (data.hints || []).map(h => `<div class="alert alert-${esc(h.level)}">${esc(h.message)}</div>`).join('');
            if (data.plan && data.plan.length) {
                const cols = Object.keys(data.plan[0]);
                html += '<div class="table-wrap" style="border:1px solid #334155;border-radius:8px;"><table><thead><tr>'
                    + cols.map(c => `<th>${esc(c)}</th>`).join('')
                    + '</tr></thead><tbody>'
                    + data.plan.map(row => '<tr>' + cols.map(c => `<td><code>${esc(row[c] === null ? 'NULL' : row[c])}</code></td>`).join('') + '</tr>').join('')
                    + '</tbody></table></div>';
            }
            box.innerHTML = html;
        } catch (err) {
            box.innerHTML = `<div class="alert alert-danger">Lỗi EXPLAIN: ${esc(err.message)}</div>`;
        }
    };

    window.addEventListener('resize', () => {
        if (lastData) {
            drawThroughputChart(lastData.series);
            drawLatencyChart(lastData.series, lastData.slow_threshold_ms);
            drawTypesChart(lastData.types);
            layoutRiver();
        }
    });

    // Initial boot
    fetchLive();
    scheduleNext();
    requestAnimationFrame(drawRiverFrame);
})();
</script>
@endsection
