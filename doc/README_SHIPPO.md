# 📦 Shippo Purchase Order API - Complete Analysis

## 📄 Documentation Summary

Tôi đã tạo 5 tài liệu chi tiết về Shippo API cho bạn:

### 1. **SHIPPO_API_GUIDE.md** 📋
**Nội dung chính:**
- Tổng quan về API Shippo trong Phoenix
- Input data structure (dữ liệu đầu vào)
  - `Shippo_Order::create()` payload
  - Request parameters
- Output data structure (dữ liệu đầu ra)
  - Success/Error responses
  - Database stored data
- Staff account usage workflow
- Excel import template format
- Error handling guide
- Related code references

**Dùng khi:** Cần hiểu cấu trúc API chi tiết

---

### 2. **SHIPPO_TESTING_GUIDE.md** 🧪
**Nội dung chính:**
- Chuẩn bị môi trường testing
- 4 scenarios với cURL examples:
  1. Import orders via CSV
  2. Create label & get rates
  3. Create transaction & generate label
  4. Error handling scenarios
- Test cases cho US/International/Error cases
- Debugging & logs guide
- Troubleshooting common issues
- Complete test workflow (5-10 minutes)

**Dùng khi:** Muốn test API thực tế

---

### 3. **SHIPPO_PAYLOAD_EXAMPLES.md** 💾
**Nội dung chính:**
- Chi tiết payload cho US addresses
- Chi tiết payload cho International addresses
- Transaction request/response
- Error response examples
- SQL database records sau mỗi step
- Complete test data SQL

**Dùng khi:** Cần xem ví dụ cụ thể về data

---

### 4. **SHIPPO_QUICK_REFERENCE.md** ⚡
**Nội dung chính:**
- Quick start guide (5 bước)
- Step-by-step workflow
- Common issues & solutions
- Data flow diagram
- Database storage locations
- Useful links
- Best practices & checklist
- Performance stats
- Training tips for staff

**Dùng khi:** Muốn hướng dẫn staff hoặc quick lookup

---

### 5. **SHIPPO_EXCEL_TEMPLATE.md** 📊
**Nội dung chính:**
- Required columns đúng thứ tự
- Example data rows (5 test cases)
- Upload instructions
- Expected outputs
- Column requirements

**Dùng khi:** Cần tạo Excel file để import

---

## 🔑 Key Findings

### Input Structure (Đầu Vào)

**Shippo_Order::create()** receives:
```json
{
    "from_address": {
        "object_purpose": "PURCHASE",
        "name": "Warehouse Name",
        "street1": "...",
        "city": "...",
        "country": "US"
    },
    "to_address": {
        "object_purpose": "PURCHASE",
        "name": "Customer Name",
        "street1": "...",
        "country": "US" or "GB" etc
    },
    "weight": 2.5,
    "order_number": "ORD-001",
    "items": [{"title": "...", "total_amount": 2}]
}
```

### Output Structure (Đầu Ra)

**Shippo_Order::create()** returns:
```json
{
    "object_id": "order_xxxxx",
    "rates": [
        {
            "object_id": "rate_1",
            "provider": "USPS",
            "amount": "5.50",
            "estimated_days": 3
        },
        ...
    ]
}
```

**Shippo_Transaction::create()** returns:
```json
{
    "status": "SUCCESS",
    "tracking_number": "1234567890123",
    "label_download": {"href": "https://...pdf"}
}
```

---

## 🔄 Complete Workflow

```
1️⃣  Staff imports Excel file
    ↓ (StaffOrderService::storeOrderCsv)
    Creates: Order, OrderAddress, OrderProduct records

2️⃣  Staff clicks "Create Label"
    ↓ (StaffOrderService::storeLabelMyib)
    Creates: OrderPackage
    Calls: Shippo_Order::create()
    Gets: 3-5 available rates

3️⃣  Staff selects a rate
    ↓ (StaffOrderController::storeRate)
    Calls: Shippo_Order::create() again
    Calls: Shippo_Transaction::create()
    
4️⃣  API returns:
    - tracking_number
    - label_url (PDF)
    - tracking_status
    
5️⃣  Data saved:
    OrderTransaction table with all above fields
    Staff can download label & track shipment
```

---

## 📍 File Locations in Code

| What | File | Lines |
|------|------|-------|
| Main service logic | `app/Services/Staff/StaffOrderService.php` | 1700-1860 |
| Controller | `app/Http/Controllers/Staff/StaffOrderController.php` | 1635-1660 |
| Model | `app/Models/OrderTransaction.php` | 1-70 |
| Import | `app/Imports/Staff/StaffOrdersImport.php` | 1-100+ |
| API Key | `.env.prod` | line 62 |

---

## 🚀 How to Use for Testing

### Option 1: Quick Test (5 minutes)

1. Read: **SHIPPO_QUICK_REFERENCE.md** (Section: Quick Start)
2. Create: Excel file with 2 test orders
3. Test: Upload → Create Label → Select Rate → Get Label

### Option 2: Full Understanding (15 minutes)

1. Read: **SHIPPO_API_GUIDE.md** (Section: 1-3)
2. Read: **SHIPPO_PAYLOAD_EXAMPLES.md** (Section: 1-2)
3. Understand: Input/Output structures

### Option 3: Deep Testing (30+ minutes)

1. Read: **SHIPPO_TESTING_GUIDE.md** (All sections)
2. Follow: Test Cases 1-4
3. Debug: Using logs from Troubleshooting section
4. Reference: **SHIPPO_PAYLOAD_EXAMPLES.md** for detailed data

### Option 4: Staff Training

1. Share: **SHIPPO_QUICK_REFERENCE.md**
2. Let staff do: Steps 1-5 in Quick Start section
3. Answer using: "Common Issues & Solutions" section

---

## 🎯 Staff Account Requirements

```sql
-- Staff user must have:
role = 'staff'              -- Not 'user' or 'admin'
status = 'active'           -- Not 'inactive'
email verified              -- Must be able to login
```

### Access Endpoints
```
POST   /staff/orders/csv-import                    -- Import Excel
POST   /staff/orders/{order_id}/store-label        -- Get rates
POST   /staff/orders/{order_id}/store-rate         -- Create label
```

---

## 🔍 How to Monitor/Debug

### Real-time Logs
```bash
tail -f storage/logs/laravel.log | grep -i "shippo\|store_rate\|payload"
```

### Database Queries
```php
// Check created orders
$orders = Order::where('user_id', 1)->orderBy('created_at', 'DESC')->get();

// Check transaction status
$transaction = OrderTransaction::where('order_id', 123)->first();
dd($transaction->tracking_number, $transaction->label_url);

// Check rates
$rates = OrderRate::where('order_id', 123)->get();
```

### Key Log Messages to Watch
```
✓ "Payload:: " - Input data
✓ "STAFF_STORE_RATE:: shippoOrder:" - Shippo response
✓ "shippoTransactionPayload::" - Transaction creation
✓ "status: SUCCESS" - Label generated
```

---

## ❓ FAQ

**Q: Tôi cần test với tài khoản staff?**
A: Có, chỉ staff mới có quyền import orders và create labels.

**Q: Dữ liệu input từ đâu?**
A: Từ Excel file import -> fields được validate -> tạo Order record

**Q: Dữ liệu output là gì?**
A: Tracking number + Label URL + Tracking status -> lưu vào OrderTransaction table

**Q: Làm sao biết API thành công?**
A: Check `order_transactions.tracking_number` - không null = thành công

**Q: Có test mode không?**
A: Có, Shippo có test mode - check cấu hình Shippo account

**Q: International orders xử lý khác?**
A: Có, dùng warehouse address + PURCHASE purpose + lấy international carriers

**Q: Rate hết hạn bao lâu?**
A: Khoảng 5 phút - phải re-fetch nếu chờ lâu

---

## 📚 Related Documentation

- **API Docs**: See [SHIPPO_API_GUIDE.md](./SHIPPO_API_GUIDE.md)
- **Testing**: See [SHIPPO_TESTING_GUIDE.md](./SHIPPO_TESTING_GUIDE.md)
- **Payloads**: See [SHIPPO_PAYLOAD_EXAMPLES.md](./SHIPPO_PAYLOAD_EXAMPLES.md)
- **Quick Ref**: See [SHIPPO_QUICK_REFERENCE.md](./SHIPPO_QUICK_REFERENCE.md)
- **Excel**: See [SHIPPO_EXCEL_TEMPLATE.md](./SHIPPO_EXCEL_TEMPLATE.md)

---

## ✅ Next Steps

1. **Hiểu API**: Read SHIPPO_API_GUIDE.md
2. **Prepare Data**: Create Excel file following SHIPPO_EXCEL_TEMPLATE.md
3. **Test**: Follow SHIPPO_TESTING_GUIDE.md scenarios
4. **Debug**: Use logs and SQL queries from this document
5. **Monitor**: Check order_transactions table for results

---

**Created**: 2024-05-09
**Status**: ✅ Complete Documentation
**Language**: Vietnamese/English

Mọi câu hỏi, hãy refer đến các tài liệu trên! 🎯
