@extends('query-monitor::layout')
@section('title', 'Cấu trúc & Index Database')

@section('content')
<div class="card">
    <div class="flex-between" style="margin-bottom:14px;">
        <div>
            <h2 style="margin:0;">Thống kê bảng &amp; Index — Database <code>{{ $info['database'] }}</code></h2>
            <div class="muted" style="font-size:13px;">Sắp xếp theo dung lượng lớn nhất để ưu tiên tối ưu index</div>
        </div>
        <div class="muted">Tổng số bảng: <strong>{{ count($info['tables']) }}</strong></div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tên bảng</th>
                    <th>Engine</th>
                    <th class="num">Số dòng (~)</th>
                    <th class="num">Data (MB)</th>
                    <th class="num">Index (MB)</th>
                    <th class="num">Tổng (MB)</th>
                    <th>Danh sách Index hiện có</th>
                </tr>
            </thead>
            <tbody>
                @foreach($info['tables'] as $t)
                    <tr>
                        <td><strong>{{ $t['table_name'] }}</strong></td>
                        <td class="muted">{{ $t['engine'] }}</td>
                        <td class="num">{{ number_format((int)$t['approx_rows']) }}</td>
                        <td class="num">{{ number_format((float)$t['data_mb'], 2) }}</td>
                        <td class="num">{{ number_format((float)$t['index_mb'], 2) }}</td>
                        <td class="num"><strong>{{ number_format((float)$t['total_mb'], 2) }}</strong></td>
                        <td>
                            @forelse($t['indexes'] as $idx)
                                <div style="font-size:12px;margin-bottom:3px;">
                                    <span class="badge {{ (int)$idx['non_unique'] === 0 ? 'badge-ok' : 'badge-other' }}">
                                        {{ $idx['index_name'] }}
                                    </span>
                                    <code>({{ $idx['columns'] }})</code>
                                </div>
                            @empty
                                <span class="badge badge-slow">Không có index!</span>
                            @endforelse
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
