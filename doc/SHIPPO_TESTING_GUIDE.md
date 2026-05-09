# Shippo API Testing Guide - Staff Account

## Mục Lục
1. [Chuẩn Bị Môi Trường](#chuẩn-bị-môi-trường)
2. [API Request/Response Examples](#api-requestresponse-examples)
3. [Test Scenarios](#test-scenarios)
4. [Debugging & Logs](#debugging--logs)
5. [Troubleshooting](#troubleshooting)

---

## Chuẩn Bị Môi Trường

### Bước 1: Xác Nhận Staff Account

```bash
# Check staff user in database
php artisan tinker

>>> $staff = App\Models\User::where('role', 'staff')->first();
>>> dd($staff);

// Expected output:
// id => 1
// email => "staff@example.com"
// role => "staff"
// status => "active"
```

### Bước 2: Xác Nhận Shippo API Key

```php
// In config/services.php
'shippo' => [
    'key' => env('SHIPPO_API_KEY'),
]

// Or check .env.prod
SHIPPO_API_KEY=03fb414bab870f6e329481fb03d0108d0998f5c9
```

### Bước 3: Kiểm Tra Warehouse Data

```bash
php artisan tinker

>>> $warehouse = App\Models\Warehouse::where('type', 'B')->first();
>>> dd($warehouse);

// Expected fields:
// sender_name, sender_company, sender_street, sender_city, 
// sender_province, sender_zip, sender_country
```

---

## API Request/Response Examples

### Scenario 1: Import Orders via CSV (Staff)

**Endpoint**:
```
POST /staff/orders/csv-import
```

**Headers**:
```http
Authorization: Bearer {staff_sanctum_token}
Content-Type: multipart/form-data
```

**Body** (form-data):
```
file: [Excel file]
user_id: 1
```

**cURL Example**:
```bash
curl -X POST http://localhost:8000/staff/orders/csv-import \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
  -F "file=@shippo_orders.xlsx" \
  -F "user_id=1"
```

**Success Response (200)**:
```json
{
    "status": "success",
    "message": "Orders imported successfully",
    "data": {
        "imported_count": 2,
        "orders": [
            {
                "id": 123,
                "order_number": "ORD-001",
                "status": "pending",
                "shipping_country": "US"
            },
            {
                "id": 124,
                "order_number": "ORD-002",
                "status": "pending",
                "shipping_country": "GB"
            }
        ]
    }
}
```

**Error Response (422)**:
```json
{
    "status": "error",
    "errors": [
        "Row 2: shipping_name is required",
        "Row 3: shipping_zip must be max 20 characters"
    ]
}
```

---

### Scenario 2: Create Label & Get Rates

**Endpoint**:
```
POST /staff/orders/{order_id}/store-label
```

**Headers**:
```http
Authorization: Bearer {staff_token}
Content-Type: application/json
```

**Body**:
```json
{
    "package": {
        "length": 10,
        "width": 8,
        "height": 5,
        "weight": 2.5,
        "weight_type": "lb",
        "distance_unit": "in"
    },
    "rate_id": null
}
```

**cURL Example**:
```bash
curl -X POST http://localhost:8000/staff/orders/123/store-label \
  -H "Authorization: Bearer ..." \
  -H "Content-Type: application/json" \
  -d '{
    "package": {
        "length": 10,
        "width": 8,
        "height": 5,
        "weight": 2.5,
        "weight_type": "lb",
        "distance_unit": "in"
    }
  }'
```

**Success Response (200)**:
```json
{
    "isValid": true,
    "rates": [
        {
            "object_id": "rate_xxxxx1",
            "provider": "USPS",
            "servicelevel": {
                "token": "usps_ground",
                "name": "Ground Advantage"
            },
            "amount": "5.50",
            "currency": "USD",
            "estimated_days": 3,
            "arrives_by": "2024-05-12"
        },
        {
            "object_id": "rate_xxxxx2",
            "provider": "USPS",
            "servicelevel": {
                "token": "usps_priority",
                "name": "Priority Mail"
            },
            "amount": "12.50",
            "currency": "USD",
            "estimated_days": 1,
            "arrives_by": "2024-05-10"
        },
        {
            "object_id": "rate_xxxxx3",
            "provider": "UPS",
            "servicelevel": {
                "token": "ups_ground",
                "name": "Ground"
            },
            "amount": "18.75",
            "currency": "USD",
            "estimated_days": 5,
            "arrives_by": "2024-05-14"
        }
    ]
}
```

**Error Response (400)**:
```json
{
    "status": "error",
    "errors": [
        "Invalid package dimensions",
        "Order not found"
    ]
}
```

---

### Scenario 3: Create Transaction & Generate Label

**Endpoint**:
```
POST /staff/orders/{order_id}/store-rate
```

**Headers**:
```http
Authorization: Bearer {staff_token}
Content-Type: application/json
```

**Body**:
```json
{
    "rate": "rate_xxxxx2"
}
```

**cURL Example**:
```bash
curl -X POST http://localhost:8000/staff/orders/123/store-rate \
  -H "Authorization: Bearer ..." \
  -H "Content-Type: application/json" \
  -d '{
    "rate": "rate_xxxxx2"
  }'
```

**Success Response (200)**:
```json
{
    "status": "success",
    "data": {
        "transaction_id": "txn_yyyyy",
        "label_url": "https://shippo-prod.s3.amazonaws.com/label_2024_05_09.pdf",
        "tracking_number": "1234567890123",
        "tracking_status": "TRANSIT",
        "tracking_url": "https://tools.usps.com/go/TrackConfirmAction_input?...",
        "amount": "12.50",
        "currency": "USD",
        "estimated_delivery": "2024-05-10"
    }
}
```

**Error Response (400)**:
```json
{
    "status": "error",
    "errors": [
        "Rate ID is required",
        "Selected rate not found"
    ]
}
```

---

## Test Scenarios

### Test Case 1: US Address (Standard USPS)

**Input Excel**:
```
created_at | order_number | shipping_name | shipping_street | shipping_city | shipping_zip | shipping_province | shipping_country | lineitem_quantity | lineitem_name
2024-05-09 | UST-001      | John Smith    | 123 Main St      | New York      | 10001        | NY                | US               | 1                 | iPhone 13
```

**Expected Output**:
- ✅ Order created with status "pending"
- ✅ Multiple USPS rates available
- ✅ Transaction created successfully
- ✅ Tracking number generated (starts with digits)
- ✅ Label PDF downloadable

**Logs to Check**:
```
log::info("STAFF_STORE_RATE:: country:" . ($addressTo->country ?? ''));
Log::info("Payload:: " . json_encode($payload));
Log::info("STAFF_STORE_RATE:: shippoOrder:" . json_encode($shippoOrder));
```

---

### Test Case 2: International Address (Purchase Order)

**Input Excel**:
```
created_at | order_number | shipping_name | shipping_street | shipping_city | shipping_zip | shipping_province | shipping_country | lineitem_quantity | lineitem_name
2024-05-09 | INT-001      | Jane Doe      | 456 Oxford St    | London        | SW1A 1AA     | London            | GB               | 2                 | MacBook Pro
```

**Expected Behavior**:
- ✅ Creates PURCHASE type order (not Shipment)
- ✅ Uses warehouse address from "type B" warehouse
- ✅ Rates include international carriers (DHL, FedEx)
- ✅ May have higher shipping costs

**Special Notes**:
- Check `if (($addressTo->country ?? '') != 'US' && ($addressTo->country ?? '') != 'United States')`
- `object_purpose` = "PURCHASE" for both addresses
- Warehouse data populated from `$warehouse` (type B)

---

### Test Case 3: Error Handling - Missing Required Fields

**Input Excel**:
```
order_number | shipping_name | shipping_street | shipping_city | shipping_zip | shipping_country | lineitem_quantity
ERR-001      | [EMPTY]       | 123 St          | NY            | 10001        | US               | 1
```

**Expected Error**:
```json
{
    "isValid": false,
    "errors": [
        "Row 1: shipping_name is required"
    ]
}
```

---

### Test Case 4: Error Handling - Invalid Rate Selection

**Steps**:
1. Import order successfully
2. Get rates for order
3. Wait 5+ minutes (rates expire)
4. Try to select same rate

**Expected Error**:
```json
{
    "status": "error",
    "errors": ["Selected rate not found. Please refresh the page and try again."]
}
```

---

## Debugging & Logs

### Log Files Location

```
storage/logs/laravel.log
```

### Enable Verbose Logging

**In `.env`**:
```
LOG_LEVEL=debug
```

### Key Log Entries to Monitor

```php
// Step 1: Payload creation
"Payload:: " . json_encode($payload)

// Step 2: Shippo Order response
"STAFF_STORE_RATE:: shippoOrder:" . json_encode($shippoOrder)

// Step 3: Transaction creation
"shippoTransactionPayload:: ".json_encode($shippoTransactionPayload)

// Step 4: Transaction response
// Check if status == 'SUCCESS'
```

### Debug Queries

```php
// Check staff orders created
$orders = Order::where('user_id', $staffId)->get();

// Check order addresses
$addresses = OrderAddress::where('order_id', $orderId)->get();

// Check order rates from Shippo
$rates = OrderRate::where('order_id', $orderId)->get();

// Check transactions
$transaction = OrderTransaction::where('order_id', $orderId)->first();
dd($transaction->tracking_number);
```

---

## Troubleshooting

### Issue 1: "Rate ID is required"

**Cause**: Not selecting a rate before purchasing
**Solution**:
1. Call `/store-label` endpoint first
2. Get rates from response
3. Select one rate ID from the response
4. Call `/store-rate` with that ID

---

### Issue 2: "Selected rate not found"

**Cause**: Rate has expired (older than 5 minutes usually)
**Solution**:
1. Call `/store-label` again to refresh rates
2. Select a fresh rate
3. Immediately call `/store-rate`

---

### Issue 3: "Country not supported"

**Cause**: Shippo doesn't support that country pair
**Solution**:
1. Test with US to US first
2. Then test with US to major countries (GB, CA, AU)
3. Check Shippo documentation for supported countries

---

### Issue 4: No Tracking Number in Response

**Cause**: Transaction status is not "SUCCESS"
**Solution**:
1. Check logs for shippo transaction response
2. Verify all address fields are filled
3. Verify weight > 0

```php
// In logs, look for:
if ($transaction['status'] != 'SUCCESS'){
    // Errors here
}
```

---

### Issue 5: Label URL Not Accessible

**Cause**: AWS S3 URL expired or permission denied
**Solution**:
1. Download immediately after creation
2. Store label_url in database before expiration
3. Re-generate transaction if needed

---

## Stafford Testing Workflow

### Complete Test Flow (5-10 minutes)

```bash
# 1. Login as staff
# Navigate to: http://localhost:8000/staff/orders

# 2. Create test Excel file with above data
# File: shippo_orders_test.xlsx

# 3. Upload file
POST /staff/orders/csv-import

# 4. Verify orders created
# Check database:
SELECT * FROM orders WHERE user_id = 1 ORDER BY created_at DESC;

# 5. Create package and get rates
POST /staff/orders/123/store-label
Body: { "package": { "length": 10, "width": 8, "height": 5, "weight": 2.5, "weight_type": "lb" } }

# 6. Verify rates returned
# Should have 3-5 rates from different carriers

# 7. Purchase shipping label
POST /staff/orders/123/store-rate
Body: { "rate": "rate_xxxxx2" }

# 8. Verify transaction created
SELECT * FROM order_transactions WHERE order_id = 123;

# 9. Download and view label
# Use label_url from response

# 10. Check tracking number
# Try to track on USPS/UPS website
```

---

## Reference URLs

- **Project**: http://localhost:8000
- **Staff Dashboard**: http://localhost:8000/staff/dashboard
- **Orders List**: http://localhost:8000/staff/orders
- **Shippo API Docs**: https://goshippo.com/docs/reference/
- **Shippo Test Mode**: Set in Shippo account settings

