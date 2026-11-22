<?php

return [
    // Bật/tắt toàn bộ module (có thể tắt nhanh qua .env mà không cần sửa code)
    'enabled' => env('QUERY_MONITOR_ENABLED', true),

    // Đường dẫn truy cập giao diện quản trị module
    'route_prefix' => env('QUERY_MONITOR_PREFIX', 'query-monitor'),

    // Ngưỡng (ms) để đánh dấu truy vấn là chậm (slow query)
    'slow_threshold_ms' => (float) env('QUERY_MONITOR_SLOW_MS', 100),

    // Số lần lặp tối thiểu của cùng 1 mẫu query trong 1 request để cảnh báo N+1
    'duplicate_threshold' => (int) env('QUERY_MONITOR_DUPLICATE_THRESHOLD', 3),

    // Số ngày lưu giữ log mặc định
    'retention_days' => (int) env('QUERY_MONITOR_RETENTION_DAYS', 14),

    // Múi giờ hiển thị trên giao diện & biểu đồ (mặc định GMT+7)
    'timezone' => env('QUERY_MONITOR_TIMEZONE', 'Asia/Ho_Chi_Minh'),

    // Đường dẫn file SQLite riêng của module
    'sqlite_path' => env('QUERY_MONITOR_SQLITE_PATH', storage_path('query-monitor/monitor.sqlite')),

    // Bỏ qua các URI bắt đầu bằng prefix sau khi ghi log
    'ignored_paths' => [
        'query-monitor',
        '_debugbar',
        'favicon.ico',
    ],
];
