<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập — Query Monitor</title>
    <style>
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: #0f172a; color: #e2e8f0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .box {
            width: 100%; max-width: 400px; background: #1e293b; border: 1px solid #334155;
            border-radius: 12px; padding: 28px; box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }
        h1 { margin: 0 0 6px; font-size: 20px; color: #fff; }
        p.sub { margin: 0 0 22px; font-size: 13px; color: #94a3b8; }
        label { display: block; font-size: 12px; color: #cbd5e1; margin-bottom: 5px; font-weight: 600; }
        input {
            width: 100%; box-sizing: border-box; padding: 10px 12px; margin-bottom: 14px;
            background: #0b1220; border: 1px solid #334155; border-radius: 7px; color: #fff; font-size: 14px;
        }
        button {
            width: 100%; padding: 10px 14px; border-radius: 7px; border: none;
            background: #0ea5e9; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer;
        }
        button:hover { background: #38bdf8; }
        .err {
            background: rgba(248,113,113,0.14); border: 1px solid rgba(248,113,113,0.4);
            color: #fecaca; padding: 10px 12px; border-radius: 7px; margin-bottom: 14px; font-size: 13px;
        }
        .ok {
            background: rgba(52,211,153,0.14); border: 1px solid rgba(52,211,153,0.4);
            color: #a7f3d0; padding: 10px 12px; border-radius: 7px; margin-bottom: 14px; font-size: 13px;
        }
    </style>
</head>
<body>
<div class="box">
    <h1>Query Monitor</h1>
    <p class="sub">Module độc lập giám sát hiệu năng truy vấn Database</p>

    @if(session('status'))
        <div class="ok">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="err">
            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
    @endif

    @if($needsSetup)
        <p style="font-size:13px;color:#fbbf24;margin-bottom:16px;">
            Chưa có tài khoản nào. Hãy tạo tài khoản Admin đầu tiên cho module:
        </p>
        <form method="POST" action="{{ route('query-monitor.setup') }}">
            @csrf
            <label>Tên hiển thị</label>
            <input type="text" name="name" value="{{ old('name', 'Monitor Admin') }}" required>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
            <label>Mật khẩu (tối thiểu 6 ký tự)</label>
            <input type="password" name="password" required>
            <label>Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" required>
            <button type="submit">Khởi tạo tài khoản Admin</button>
        </form>
    @else
        <form method="POST" action="{{ route('query-monitor.login.post') }}">
            @csrf
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
            <button type="submit">Đăng nhập</button>
        </form>
    @endif
</div>
</body>
</html>
