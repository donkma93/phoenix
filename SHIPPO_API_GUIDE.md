# Shippo Purchase Order API - Input/Output Guide

## Tổng Quan

Hệ thống Phoenix sử dụng Shippo API để tạo purchase orders (vận đơn) từ dữ liệu đơn hàng. API được gọi thông qua staff account khi nhân viên kho (warehouse staff) xử lý đơn hàng.

---

## 1. Input Data Structure (Dữ Liệu Đầu Vào)

### 1.1 Shippo_Order::create() - Tạo Purchase Order

**File**: `app/Services/Staff/StaffOrderService.php` - dòng ~1762, ~1838

**Payload Input:**
```php
[
    "total_tax" => "0.00",
    
    // Địa chỉ gửi hàng (Warehouse)
    "from_address" => [
        "object_purpose" => "PURCHASE",
        "name" => string,                      // Tên warehouse
        "company" => string,                   // Công ty warehouse
        "street1" => string,                   // Đường 1
        "city" => string,                      // Thành phố
        "state" => string,                     // Tỉnh/Bang
        "zip" => string,                       // Mã bưu điện
        "country" => string                    // Quốc gia
    ],
    
    // Địa chỉ nhận hàng (Customer)
    "to_address" => [
        "object_purpose" => "PURCHASE",
        "name" => string,                      // Tên khách
        "company" => string,                   // Công ty khách
        "street1" => string,                   // Đường 1
        "street2" => string,                   // Đường 2 (tùy chọn)
        "city" => string,                      // Thành phố
        "state" => string,                     // Tỉnh/Bang
        "zip" => string,                       // Mã bưu điện
        "country" => string,                   // Quốc gia (US = Shippo Order, Non-US = Purchase Order)
        "phone" => string                      // Điện thoại
    ],
    
    // Thông tin đơn hàng
    "weight" => float,                        // Trọng lượng
    "weight_unit" => "lb" | "kg",             // Đơn vị trọng lượng
    "order_number" => string,                 // Số đơn hàng
    "order_status" => "PAID",                 // Trạng thái (fixed: PAID)
    "placed_at" => datetime,                  // Ngày tạo
    
    // Chi phí
    "subtotal_price" => "0",                  // Subtotal
    "total_price" => "0",                     // Total
    "shipping_cost" => null,                  // Phí vận chuyển
    "shipping_cost_currency" => "USD",
    "currency" => "USD",
    
    // Sản phẩm trong đơn hàng
    "items" => [
        [
            "title" => string,                // Tên sản phẩm
            "total_amount" => int,            // Số lượng
            "weight_unit" => "lb" | "kg"     // Đơn vị trọng lượng
        ],
        ...
    ],
    
    // Thông tin khác
    "shop_app" => "Shippo",                  // Fixed
    "shipping_method" => null,                // Tùy chọn
    "hidden" => false                         // Hiển thị hay ẩn
]
```

**Ví dụ Input (Excel → Order Import):**
```excel
created_at | order_number | shipping_name | shipping_street | shipping_address1 | shipping_address2 | shipping_company | shipping_city | shipping_zip | shipping_province | shipping_country | lineitem_quantity | lineitem_name | lineitem_price
2024-05-09 | ORD-123456   | John Doe      | 123 Main St     | Unit 1           | Apt B            | ABC Corp         | New York      | 10001        | NY                | US               | 2                 | iPhone 13     | 99.99
```

### 1.2 Request Rate Parameters

**File**: `app/Http/Controllers/Staff/StaffOrderController.php` - storeRate()

```http
POST /staff/orders/{order_id}/store-rate
Authorization: Bearer {staff_token}
Content-Type: application/json

{
    "rate": "shippo_rate_object_id"  // ID từ storeLabel step
}
```

---

## 2. Output Data Structure (Dữ Liệu Đầu Ra)

### 2.1 Shippo_Order::create() Response

**Success Response:**
```json
{
    "object_id": "string",
    "object_owner": "shippo",
    "object_state": "VALID",
    "object_created": "2024-05-09T10:30:00Z",
    "object_updated": "2024-05-09T10:30:00Z",
    
    "address_from": {
        "object_id": "adr_xxxxx",
        "name": "Warehouse Name",
        "company": "Warehouse Company",
        "street1": "123 Warehouse Ave",
        "city": "Los Angeles",
        "state": "CA",
        "zip": "90001",
        "country": "US"
    },
    
    "address_to": {
        "object_id": "adr_yyyyy",
        "name": "Customer Name",
        "street1": "456 Customer St",
        "city": "New York",
        "state": "NY",
        "zip": "10001",
        "country": "US"
    },
    
    "weight": 5.5,
    "weight_unit": "lb",
    "order_number": "ORD-123456",
    "order_status": "PAID",
    "placed_at": "2024-05-09T00:00:00Z",
    
    "line_items": [
        {
            "title": "iPhone 13",
            "quantity": 2,
            "total_price": 199.98,
            "currency": "USD"
        }
    ],
    
    "total_tax": "0.00",
    "total_price": "199.98",
    "currency": "USD",
    
    "messages": [],
    "rates": [
        {
            "object_id": "rate_xxxxx",
            "object_owner": "shippo",
            "provider": "USPS",
            "servicelevel": {
                "token": "usps_priority",
                "name": "Priority Mail"
            },
            "amount": "12.50",
            "currency": "USD",
            "days": 3
        }
    ]
}
```

### 2.2 Transaction Creation Response

**File**: `app/Models/OrderTransaction.php::createTransaction()`

```http
POST /api/v1/transactions
Authorization: Bearer {shippo_key}

Input:
{
    "rate": "shippo_rate_object_id",
    "order_id": "shippo_order_object_id",
    "label_file_type": "PDF_4x6",
    "async": false
}

Success Response:
{
    "object_id": "txn_xxxxx",
    "object_owner": "shippo",
    "object_state": "VALID",
    "status": "SUCCESS",
    
    "rate": "rate_xxxxx",
    "order_id": "order_xxxxx",
    
    "tracking_number": "1234567890123",
    "tracking_status": "TRANSIT",
    "tracking_url_provider": "https://tools.usps.com/go/TrackConfirmAction_input?..."
    
    "label_download": {
        "href": "https://shippo-prod.s3.amazonaws.com/...",
        "expiresAt": "2024-05-16T10:30:00Z"
    },
    
    "commercial_invoice_download": null,
    "manifest_download": null,
    
    "submitted_datetime": "2024-05-09T10:35:00Z",
    "eta_datetime": "2024-05-12T23:59:00Z",
    "messages": [],
    "test": false
}
```

### 2.3 Database Stored Data

**Table**: `order_transactions`

```sql
INSERT INTO order_transactions (
    order_id,
    order_rate_id,
    transaction_id,
    label_url,
    tracking_number,
    tracking_status,
    tracking_url_provider
)
VALUES (
    123,                    -- Order ID
    456,                    -- Rate ID
    'txn_xxxxx',           -- Shippo Transaction ID
    'https://shippo-prod.s3.amazonaws.com/...',
    '1234567890123',       -- Tracking Number
    'SUCCESS',
    'https://tools.usps.com/go/TrackConfirmAction_input?...'
);
```

---

## 3. Staff Account Usage

### 3.1 Authentication

Staff users access the system via:
- **Route Prefix**: `/staff/*`
- **Middleware**: `auth`, `staff` (role check)
- **Model**: `App\Models\User` với `role = 'staff'`

### 3.2 Staff Order Workflow

1. **Step 1: Import Orders via Excel**
   ```
   POST /staff/orders/csv-import
   File: orders.xlsx
   ```
   - Validates order data
   - Creates `Order` records
   - Creates `OrderAddress` records

2. **Step 2: Create Label & Get Rates**
   ```
   POST /staff/orders/{order_id}/store-label
   Body: { package: {...}, rate_id: null }
   ```
   - Creates `OrderPackage` record
   - Calls Shippo to get available rates
   - Returns `OrderRate` options

3. **Step 3: Select Rate & Create Transaction**
   ```
   POST /staff/orders/{order_id}/store-rate
   Body: { rate: "rate_id" }
   ```
   - Creates Shippo Order
   - Creates Transaction (generates label)
   - Returns tracking number & label URL

### 3.3 Authorization Checks

```php
// Only staff can access:
Route::middleware(['auth:sanctum', 'staff'])->group(function () {
    Route::post('/orders/csv-import', [StaffOrderController::class, 'storeOrderCsv']);
    Route::post('/orders/{order_id}/store-label', [StaffOrderController::class, 'storeLabelMyib']);
    Route::post('/orders/{order_id}/store-rate', [StaffOrderController::class, 'storeRate']);
});
```

---

## 4. Excel Import Template

### 4.1 Required Columns

```
Column A: created_at (YYYY-MM-DD format, optional)
Column B: order_number (Required, max 255)
Column C: shipping_name (Required, max 255)
Column D: shipping_street (Required, max 255)
Column E: shipping_address1 (Optional)
Column F: shipping_address2 (Optional)
Column G: shipping_company (Optional)
Column H: shipping_city (Required, max 255)
Column I: shipping_zip (Required, max 20)
Column J: shipping_province (Required, max 255)
Column K: shipping_country (Required, max 255)
Column L: lineitem_quantity (Required, integer, min 1)
Column M: lineitem_name (Required, max 255)
Column N: lineitem_price (Optional, numeric)
```

### 4.2 Example Excel Data

| created_at | order_number | shipping_name | shipping_street | shipping_address1 | shipping_address2 | shipping_company | shipping_city | shipping_zip | shipping_province | shipping_country | lineitem_quantity | lineitem_name | lineitem_price |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-05-09 | ORD-001 | John Smith | 123 Main Street | Apt 4B | | Acme Corp | New York | 10001 | NY | US | 2 | iPhone 13 Pro | 999.99 |
| 2024-05-09 | ORD-002 | Jane Doe | 456 Oak Avenue | Suite 100 | | Tech Store | Los Angeles | 90001 | CA | US | 1 | MacBook Pro | 1999.99 |

---

## 5. Error Handling

### 5.1 Common Errors

| Error | Cause | Fix |
|---|---|---|
| `Rate ID is required` | Missing rate parameter | Select a rate from storeLabel response |
| `Selected rate not found` | Rate expired or not found | Re-fetch rates via storeLabel |
| `Country not supported` | Non-US addresses require PURCHASE purpose | Check address country code |
| `Invalid address format` | Missing required address fields | Verify all required fields in Excel |

### 5.2 Response with Errors

```json
{
    "status": "error",
    "errors": [
        "Invalid address format",
        "Weight is required"
    ]
}
```

---

## 6. Testing with Excel File

### 6.1 Download Test Template
- File: `resources/templates/orders_import_template.xlsx`
- Location in project: Create if not exists

### 6.2 Test Steps
1. Login as staff user
2. Navigate to `/staff/orders`
3. Upload Excel file
4. Select rate for each order
5. Confirm transaction
6. View label and tracking number

### 6.3 Verify Output
- Check `order_transactions` table for tracking_number
- Verify label_url is accessible (download PDF)
- Confirm tracking_status via Shippo webhook

---

## 7. Related Code References

- **Service Implementation**: `app/Services/Staff/StaffOrderService.php` (lines 1700-1860)
- **Controller**: `app/Http/Controllers/Staff/StaffOrderController.php` (lines 1635-1660)
- **Model**: `app/Models/OrderTransaction.php`
- **Import**: `app/Imports/Staff/StaffOrdersImport.php`
- **Request Validation**: `app/Http/Requests/Staff/StoreLabelRequest.php`
- **Configuration**: `config/services.php` (Shippo API key)

---

## 8. Shippo Library Reference

**Package**: `shippo/shippo-php ^1.4`

**Classes Used**:
- `Shippo_Order::create($payload)` - Create purchase order
- `Shippo_Shipment::all()` - List shipments
- `Shippo_Transaction::create($payload)` - Create transaction & label
- `Shippo_Track::get_status($params)` - Get tracking status

**Documentation**: https://github.com/goshippo/shippo-php-client
