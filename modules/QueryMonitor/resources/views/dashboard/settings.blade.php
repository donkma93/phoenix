@extends('query-monitor::layout')
@section('title', 'Cấu hình Query Monitor')

@section('content')
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(400px, 1fr));gap:20px;">
    <div class="card">
        <h2>Cấu hình ghi nhận truy vấn</h2>
        <form method="POST" action="{{ route('query-monitor.settings.update') }}">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block;margin-bottom:6px;font-weight:600;">Trạng thái ghi log</label>
                <select name="recording_enabled" style="width:100%;">
                    <option value="1" {{ $recordingEnabled ? 'selected' : '' }}>Đang bật (Ghi nhận mọi truy vấn)</option>
                    <option value="0" {{ !$recordingEnabled ? 'selected' : '' }}>Tạm dừng (Không ghi log mới)</option>
                </select>
            </div>
            <div style="margin-bottom:18px;">
                <label style="display:block;margin-bottom:6px;font-weight:600;">Ngưỡng Slow Query (ms)</label>
                <input type="number" step="1" min="1" max="60000" name="slow_threshold_ms" value="{{ $slowThresholdMs }}" style="width:100%;">
                <div class="muted" style="font-size:12px;margin-top:4px;">Các truy vấn chạy lâu hơn ngưỡng này sẽ được đánh dấu màu đỏ (SLOW).</div>
            </div>
            <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
        </form>
    </div>

    <div class="card">
        <h2>Quản lý dữ liệu lưu trữ (SQLite riêng)</h2>
        <div style="margin-bottom:14px;font-size:13px;">
            <div><strong>File SQLite:</strong> <code>{{ $sqlitePath }}</code></div>
            <div style="margin-top:4px;"><strong>Dung lượng hiện tại:</strong> {{ number_format($sqliteSizeBytes / 1048576, 2) }} MB</div>
            <div class="muted" style="margin-top:6px;">Dữ liệu giám sát lưu hoàn toàn tách biệt trong file SQLite này, không ảnh hưởng tới MySQL chính.</div>
        </div>

        <form method="POST" action="{{ route('query-monitor.settings.purge') }}" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
            @csrf
            <input type="hidden" name="days" value="7">
            <button type="submit" class="btn" onclick="return confirm('Xóa các log cũ hơn 7 ngày?')">Dọn log cũ hơn 7 ngày</button>
        </form>

        <form method="POST" action="{{ route('query-monitor.settings.purge') }}">
            @csrf
            <button type="submit" class="btn btn-danger" onclick="return confirm('Xóa TOÀN BỘ log truy vấn hiện tại?')">Xóa toàn bộ log truy vấn</button>
        </form>
    </div>
</div>
@endsection
