# Cấu hình MyIB API

## Cấu hình .env

Thêm các dòng sau vào file `.env`:

```env
MYIB_EMAIL=your_email@example.com
MYIB_PASSWORD=your_password
```

## Thông tin xác thực

MyIB API sử dụng **Basic Authentication** với:
- **Username**: Email đăng nhập
- **Password**: Mật khẩu đăng nhập
- **Header**: `Authorization: Basic {base64(email:password)}`

## Đã cập nhật

✅ **config/app.php**
- Thêm `myib_email` và `myib_password` từ `.env`

✅ **app/Services/Staff/StaffOrderService.php**
- `getMyibRates()` - Đã cập nhật để dùng Basic Auth
- `createMyibTransaction()` - Đã cập nhật để dùng Basic Auth

## Cách hoạt động

1. Lấy email và password từ config
2. Encode `email:password` bằng base64
3. Gửi header: `Authorization: Basic {encoded_string}`
4. Content-Type: `application/json`

## Ví dụ

```php
$email = config('app.myib_email');
$password = config('app.myib_password');
$basicAuth = base64_encode($email . ':' . $password);

// Header sẽ là:
// Authorization: Basic dXNlckBleGFtcGxlLmNvbTpwYXNzd29yZA==
```

