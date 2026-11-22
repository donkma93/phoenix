@extends('query-monitor::layout')
@section('title', 'Nhật ký truy vấn')

@section('content')
<div class="card">
    <h2>Nhật ký truy vấn chi tiết ({{ number_format($result['total']) }} bản ghi)</h2>
    <form method="GET" class="filters">
        <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm trong SQL, URI, Caller..." style="min-width:240px;">
        <input type="text" name="uri" value="{{ $filters['uri'] }}" placeholder="Lọc theo URI..." style="min-width:160px;">
        <select name="query_type">
            <option value="">Tất cả loại SQL</option>
            @foreach(['SELECT','INSERT','UPDATE','DELETE'] as $t)
                <option value="{{ $t }}" {{ $filters['query_type'] === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        <input type="number" step="1" name="min_ms" value="{{ $filters['min_ms'] }}" placeholder="Tối thiểu ms" style="width:120px;">
        <select name="range">
            @foreach($ranges as $key => $info)
                <option value="{{ $key }}" {{ $range === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
            @endforeach
        </select>
        <select name="sort">
            <option value="latest" {{ $filters['sort'] === 'latest' ? 'selected' : '' }}>Mới nhất</option>
            <option value="slowest" {{ $filters['sort'] === 'slowest' ? 'selected' : '' }}>Chậm nhất trước</option>
        </select>
        <label style="display:flex;align-items:center;gap:5px;cursor:pointer;">
            <input type="checkbox" name="slow_only" value="1" {{ $filters['slow_only'] ? 'checked' : '' }}> Chỉ Slow
        </label>
        <label style="display:flex;align-items:center;gap:5px;cursor:pointer;">
            <input type="checkbox" name="n1_only" value="1" {{ $filters['n1_only'] ? 'checked' : '' }}> Chỉ N+1
        </label>
        <button type="submit" class="btn btn-primary">Lọc</button>
        <a href="{{ route('query-monitor.queries') }}" class="btn">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Thời gian (GMT+7)</th>
                    <th>Loại</th>
                    <th>SQL &amp; Bindings</th>
                    <th>Endpoint / Caller</th>
                    <th class="num">Thời gian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($result['items'] as $row)
                    <tr>
                        <td style="white-space:nowrap;" class="muted">{{ $row['created_at'] }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($row['query_type']) }}">{{ $row['query_type'] }}</span>
                            @if((int)$row['is_slow'] === 1)
                                <div style="margin-top:4px;"><span class="badge badge-slow">SLOW</span></div>
                            @endif
                            @if((int)$row['duplicate_in_request'] >= 3)
                                <div style="margin-top:4px;"><span class="badge badge-n1">&times;{{ $row['duplicate_in_request'] }}</span></div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('query-monitor.fingerprint', $row['sql_hash']) }}">
                                <div class="sql-code">{{ \Illuminate\Support\Str::limit($row['sql_raw'], 320) }}</div>
                            </a>
                            @if(!empty($row['bindings_json']) && $row['bindings_json'] !== '[]')
                                <div class="muted" style="font-size:11px;margin-top:3px;">Bindings: <code>{{ \Illuminate\Support\Str::limit($row['bindings_json'], 180) }}</code></div>
                            @endif
                        </td>
                        <td>
                            <div><span class="badge badge-other">{{ $row['http_method'] }}</span> {{ \Illuminate\Support\Str::limit($row['uri'], 45) }}</div>
                            <div class="muted" style="font-size:11px;margin-top:3px;">{{ $row['caller'] ?: '-' }}</div>
                        </td>
                        <td class="num" style="color:{{ (int)$row['is_slow'] === 1 ? 'var(--danger)' : 'inherit' }}">
                            <strong>{{ number_format((float)$row['time_ms'], 2) }} ms</strong>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted" style="text-align:center;padding:24px;">Không có truy vấn nào khớp bộ lọc.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($result['last_page'] > 1)
        <div class="flex-between" style="margin-top:16px;">
            <div class="muted">Trang {{ $result['page'] }} / {{ $result['last_page'] }}</div>
            <div style="display:flex;gap:8px;">
                @if($result['page'] > 1)
                    <a class="btn btn-sm" href="{{ request()->fullUrlWithQuery(['page' => $result['page'] - 1]) }}">&larr; Trang trước</a>
                @endif
                @if($result['page'] < $result['last_page'])
                    <a class="btn btn-sm" href="{{ request()->fullUrlWithQuery(['page' => $result['page'] + 1]) }}">Trang sau &rarr;</a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
