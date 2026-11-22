@extends('query-monitor::layout')
@section('title', 'Quản lý tài khoản Query Monitor')

@section('content')
<div class="card">
    <h2>Thêm tài khoản Query Monitor mới</h2>
    <p class="muted" style="margin-top:-8px;margin-bottom:14px;font-size:13px;">
        Tài khoản này hoàn toàn độc lập với bảng <code>users</code> của hệ thống chính.
    </p>
    <form method="POST" action="{{ route('query-monitor.users.store') }}" class="filters">
        @csrf
        <input type="text" name="name" placeholder="Tên hiển thị" required>
        <input type="email" name="email" placeholder="Email đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu (>= 6 ký tự)" required>
        <select name="role">
            <option value="admin">Admin (Toàn quyền)</option>
            <option value="viewer">Viewer (Chỉ xem &amp; EXPLAIN)</option>
        </select>
        <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
    </form>
</div>

<div class="card">
    <h2>Danh sách tài khoản ({{ count($users) }})</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Quyền</th>
                    <th>Trạng thái</th>
                    <th>Đăng nhập gần nhất</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>#{{ $u['id'] }}</td>
                        <td><strong>{{ $u['name'] }}</strong></td>
                        <td>{{ $u['email'] }}</td>
                        <td><span class="badge {{ $u['role'] === 'admin' ? 'badge-select' : 'badge-other' }}">{{ $u['role'] }}</span></td>
                        <td>
                            @if((int)$u['is_active'] === 1)
                                <span class="badge badge-ok">Hoạt động</span>
                            @else
                                <span class="badge badge-slow">Đã khóa</span>
                            @endif
                        </td>
                        <td class="muted">{{ $u['last_login_at'] ?: 'Chưa đăng nhập' }}</td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                                <form method="POST" action="{{ route('query-monitor.users.password', $u['id']) }}" style="display:flex;gap:4px;">
                                    @csrf
                                    <input type="password" name="password" placeholder="Mật khẩu mới" style="width:130px;padding:4px 8px;font-size:12px;" required>
                                    <button type="submit" class="btn btn-sm">Đổi MK</button>
                                </form>

                                @if((int)$u['id'] !== (int)($monitorUser['id'] ?? 0))
                                    <form method="POST" action="{{ route('query-monitor.users.toggle', $u['id']) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm">{{ (int)$u['is_active'] === 1 ? 'Khóa' : 'Mở' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('query-monitor.users.destroy', $u['id']) }}" onsubmit="return confirm('Xóa tài khoản này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
