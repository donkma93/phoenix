# Cấu trúc BUY LABELS VIA MYIB

## Tổng quan
Hệ thống hỗ trợ tạo shipping labels thông qua MyIB API với 2 luồng chính:
1. **Tạo label đơn lẻ** (Single Order)
2. **Import Excel hàng loạt** (Bulk Import)

---

## 1. Routes (routes/web.php)

```php
// Tạo label đơn lẻ
Route::post('/orders/labels/create-via-myib', [StaffOrderController::class, 'createLabelMyib'])
    ->name('orders.labels.create.myib')
    ->middleware('role:picker,packer,receiver,staff,staff-epacket');

// Import Excel hàng loạt
Route::post('/labels/import-excel-myib', [StaffOrderController::class, 'importLabelMyib'])
    ->name('labels.import.excel.myib')
    ->middleware('role:picker,packer,receiver,staff,staff-epacket');
```

---

## 2. Controller (app/Http/Controllers/Staff/StaffOrderController.php)

### 2.1. `createLabelMyib()` - Tạo label đơn lẻ
- **Input**: `StoreLabelRequest` với thông tin order, package, shipping address
- **Flow**:
  1. Validate request
  2. Gọi `storeLabelMyib()` từ Service
  3. Nếu thành công → redirect đến trang chọn rate
  4. Nếu lỗi → redirect back với error messages

### 2.2. `importLabelMyib()` - Import Excel hàng loạt
- **Input**: File Excel/CSV
- **Flow**:
  1. Gọi `storeExcelMyib()` từ Service
  2. Validate file và dữ liệu
  3. Xử lý từng dòng trong file
  4. Trả về kết quả với danh sách lỗi (nếu có)

---

## 3. Service (app/Services/Staff/StaffOrderService.php)

### 3.1. `storeLabelMyib($request, $orderId)` - Tạo label và lấy rates
**Chức năng chính:**
- Cập nhật thông tin package (dimensions, weight)
- Validate và tạo shipping address (from)
- Convert dimensions và weight sang format MyIB
- Gọi MyIB API để lấy rates (12 loại shapes khác nhau)
- Lưu rates vào bảng `order_rates`

**Các helper methods:**
- `convertDimensionsToMyib()`: Convert inch → cm
- `convertWeightToMyib()`: Convert lb/kg → oz
- `prepareMyibPayload()`: Chuẩn bị payload cho API
- `getMyibRates()`: Gọi API lấy rates
- `setMyibRates()`: Convert và lưu rates vào DB

### 3.2. `storeExcelMyib($file, $request)` - Import Excel
**Chức năng:**
- Import file Excel sử dụng `StaffLabelsImport`
- Validate từng dòng
- Tạo label cho mỗi order_id
- **LƯU Ý**: Hiện tại chưa implement đầy đủ MyIB API integration (có TODO comments)

### 3.3. `createMyibTransaction($orderRate, $order, $orderPackage)` - Tạo transaction
**Chức năng:**
- Được gọi khi user chọn một rate từ danh sách
- Gọi MyIB API để tạo label thực tế
- Lưu tracking number và label URL vào `order_transactions`
- **LƯU Ý**: Cần verify API endpoint và response structure

---

## 4. Views

### 4.1. `resources/views/order/create_label.blade.php`
- Form tạo label đơn lẻ
- Button "Buy labels via Myib" trigger JavaScript
- Validate form trước khi submit
- Submit đến route `staff.orders.labels.create.myib`

### 4.2. `resources/views/order/import-create-label.blade.php`
- Form upload file Excel/CSV
- Section "Buy labels via Myib" (dòng 263-312)
- Hiển thị lỗi validation và import errors
- Submit đến route `staff.labels.import.excel.myib`

---

## 5. Import Class (app/Imports/Staff/StaffLabelsImport.php)

**Cấu trúc file Excel yêu cầu:**
- Header row với các cột:
  - `order_id` (required, integer)
  - `shipping_name` (required, string)
  - `shipping_country` (required, alpha_dash)
  - `shipping_province` (required, alpha_dash)
  - `shipping_city` (required, string)
  - `shipping_street` (required, string)
  - `shipping_zip` (required)
  - `package_length`, `package_width`, `package_height`, `package_weight` (nullable, numeric)
  - `size_type`, `weight_type` (nullable, numeric)
  - `shipping_phone`, `shipping_company`, `shipping_address1`, `shipping_address2` (nullable)

---

## 6. MyIB API Integration

### 6.1. Get Rates API
- **Endpoint**: `https://api.myibservices.com/v1/price`
- **Method**: POST
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {api_key}`
- **Shapes được gọi**: 12 loại
  - Priority: Parcel, FlatRateEnvelope, LegalFlatRateEnvelope, PaddedFlatRateEnvelope, SmallFlatRateBox, MediumFlatRateBox, LargeFlatRateBox
  - Express: Parcel, FlatRateEnvelope, LegalFlatRateEnvelope, PaddedFlatRateEnvelope
  - FirstClass: Parcel

### 6.2. Create Label API
- **Endpoint**: `https://api.myibservices.com/v1/label` (TODO: verify endpoint)
- **Method**: POST
- **Headers**: Tương tự Get Rates API
- **Response**: Cần verify structure (hiện tại code expect: `label_id`, `label_url`, `tracking_number`)

### 6.3. Configuration
- **Config key**: `config('app.myib_api_key')`
- **Cần thêm vào**: `.env` và `config/app.php`

---

## 7. Database Structure

### 7.1. `order_rates` table
- Lưu các rates từ MyIB API
- `object_owner` = 'myib'
- `object_id` = JSON chứa full rate data
- `attributes` = JSON chứa `mail_class` và `shape`
- `provider` = 'Myib'

### 7.2. `order_transactions` table
- Lưu transaction khi label được tạo thành công
- Chứa `tracking_number`, `label_url`, etc.

### 7.3. `order_addresses` table
- Lưu shipping address (from/to)
- Validate qua Shippo API (có thể cần điều chỉnh cho MyIB)

---

## 8. Flow Diagram

### Flow 1: Tạo label đơn lẻ
```
User clicks "Buy labels via Myib"
  ↓
JavaScript validates form
  ↓
POST /orders/labels/create-via-myib
  ↓
StaffOrderController::createLabelMyib()
  ↓
StaffOrderService::storeLabelMyib()
  ↓
- Update package info
- Validate address
- Convert dimensions/weight
- Call MyIB API (getMyibRates) - 12 requests
- Save rates to order_rates
  ↓
Redirect to rates selection page
  ↓
User selects a rate
  ↓
createMyibTransaction() called
  ↓
Call MyIB API to create label
  ↓
Save transaction to order_transactions
```

### Flow 2: Import Excel
```
User uploads Excel file
  ↓
POST /labels/import-excel-myib
  ↓
StaffOrderController::importLabelMyib()
  ↓
StaffOrderService::storeExcelMyib()
  ↓
StaffLabelsImport::collection() - validate rows
  ↓
For each row:
  - createLabel() - prepare order
  - TODO: Call MyIB API (chưa implement)
  - Save transaction
  ↓
Return results with errors
```

---

## 9. Các điểm cần lưu ý

### ⚠️ Chưa hoàn thiện:
1. **Import Excel**: Method `storeExcelMyib()` có TODO comments, chưa implement đầy đủ MyIB API calls
2. **API Endpoint**: Endpoint create label cần verify (`/v1/label`)
3. **API Response**: Cần verify structure của response từ MyIB API
4. **Config**: Cần thêm `myib_api_key` vào config
5. **Error Handling**: Cần cải thiện error handling và logging

### ✅ Đã hoàn thiện:
1. UI/UX cho cả 2 flows
2. Validation cho form và Excel
3. Rate retrieval từ MyIB API
4. Database structure
5. Address validation (dùng Shippo, có thể cần điều chỉnh)

---

## 10. Helper Methods Chi tiết

### `convertDimensionsToMyib($package)`
- Convert inch → cm (nếu `size_type == SIZE_IN`)
- Return: `['length', 'width', 'height', 'unit' => 'cm']`

### `convertWeightToMyib($package)`
- Convert lb → oz hoặc kg → oz
- Return: `['weight', 'unit' => 'oz']`

### `prepareMyibPayload($order, $dimensions, $weight)`
- Chuẩn bị payload cho API
- Split name thành first/middle/last
- Convert country name → ISO code
- Return payload với structure:
  ```php
  [
    'from_address' => [...],
    'to_address' => [...],
    'image_format' => 'png',
    'metadata' => [...],
    'weight_unit' => 'oz',
    'weight' => ...,
    'dimensions_unit' => 'cm',
    'dimensions' => [...]
  ]
  ```

### `getMyibRates($payload)`
- Gọi API 12 lần với các shapes khác nhau
- Return array of rates với `postage_amount`

### `setMyibRates($myibRates, $orderId)`
- Convert rates sang format `order_rates`
- Format service name (e.g., "LegalFlatRateEnvelope" → "Legal Flat Rate Envelope")
- Sort by amount (ascending)
- Return array ready for `OrderRate::insert()`

---

## 11. Webhook (routes/api.php)

Có webhook endpoint cho MyIB:
```php
Route::post('/myib-webhook', function (Request $request) {
    Log::info('📦 Webhook từ MyIB:', $request->all());
    // TODO: Process webhook data
});
```

---

## Kết luận

Cấu trúc "BUY LABELS VIA MYIB" đã được thiết kế khá đầy đủ với:
- ✅ UI/UX hoàn chỉnh
- ✅ Validation đầy đủ
- ✅ Rate retrieval từ API
- ⚠️ Cần hoàn thiện: Import Excel integration, Create label API verification

