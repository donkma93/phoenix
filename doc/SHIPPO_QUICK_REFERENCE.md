# Shippo API - Quick Reference for Staff

## 🚀 Quick Start

### What is Shippo?
Shippo is a shipping platform that creates purchase orders (vận đơn) and generates shipping labels with tracking numbers.

### What Can Staff Do?
1. ✅ Import orders from Excel file
2. ✅ Get shipping rates from multiple carriers (USPS, UPS, DHL, etc.)
3. ✅ Create shipping labels with tracking numbers
4. ✅ Download PDF labels
5. ✅ Track shipments

---

## 📋 Step-by-Step Workflow

### Step 1: Prepare Excel File

**Filename**: `orders_to_ship.xlsx`

**Required Columns**:
- `order_number` - Your order ID
- `shipping_name` - Customer name
- `shipping_street` - Street address
- `shipping_city` - City
- `shipping_zip` - ZIP/Postal code
- `shipping_province` - State/Province
- `shipping_country` - Country code (US, GB, CA, etc.)
- `lineitem_quantity` - Number of items
- `lineitem_name` - Item name

**Optional Columns**:
- `created_at` - Order date (YYYY-MM-DD)
- `shipping_address1` - Address line 2
- `shipping_address2` - Address line 3
- `shipping_company` - Company name
- `lineitem_price` - Item price

**Example**:
```
order_number | shipping_name | shipping_street | shipping_city | shipping_zip | shipping_province | shipping_country | lineitem_quantity | lineitem_name
ORD-001      | John Smith    | 123 Main St     | New York      | 10001        | NY                | US               | 2                 | iPhone 13
ORD-002      | Jane Doe      | 456 Oak Ave     | Los Angeles   | 90001        | CA                | US               | 1                 | MacBook Pro
```

### Step 2: Upload Orders

1. Go to: `http://localhost:8000/staff/orders`
2. Click "Import Orders"
3. Select your Excel file
4. Verify data is correct
5. Click "Import"

**Success Message**:
```
✅ 2 orders imported successfully
```

### Step 3: Create Shipping Labels

1. Find order in list: `ORD-001`
2. Click "Create Label"
3. Enter package dimensions:
   - Length: 10 in
   - Width: 8 in
   - Height: 5 in
   - Weight: 2.5 lb
4. Click "Get Rates"

**You will see** (3-5 shipping options):
```
USPS - Ground Advantage - $5.50 - 3 days
USPS - Priority Mail - $12.50 - 1 day  ⭐ RECOMMENDED
UPS - Ground - $18.75 - 5 days
```

### Step 4: Select Rate & Generate Label

1. Click on the rate you want (e.g., USPS Priority - $12.50)
2. Click "Buy Label"
3. Wait 5-10 seconds...

**Success** - You will see:
```
✅ Label created successfully

Tracking Number: 1234567890123
Carrier: USPS
Status: In Transit
Arrives by: May 10, 2024

📥 Download Label (PDF)
🔗 Track Package
```

### Step 5: Download & Print Label

1. Click "Download Label"
2. Open the PDF file
3. Print on 4x6 label printer
4. Attach to package
5. Ship! 📦

---

## 🔍 After Purchase

### Track Your Shipment

```
Order: ORD-001
Tracking Number: 1234567890123
Click: 🔗 Track Package

Status: In Transit
Last Updated: May 9, 2024 2:30 PM
Est. Delivery: May 10, 2024
```

### Download Label Anytime

```
Select Order: ORD-001
Click: 📥 Download Label
(PDF expires in 7 days)
```

---

## ⚠️ Common Issues & Solutions

### "Rate not found"
❌ Problem: You waited too long before selecting
✅ Solution: Click "Get Rates" again, then immediately select rate

### "Address validation failed"
❌ Problem: Missing required address fields
✅ Solution: Check all fields are filled in Excel:
- shipping_city: required ✓
- shipping_zip: required ✓
- shipping_country: required ✓

### "Weight too heavy"
❌ Problem: Package exceeds carrier limit
✅ Solution: Some carriers have weight limits
- USPS Ground: 70 lbs max
- UPS Ground: 150 lbs max
- Try different carrier

### "No PDF label"
❌ Problem: Label generation failed
✅ Solution:
1. Check label_url in database
2. Try downloading again within 7 days
3. Re-create label if expired

---

## 📊 API Data Flow

```
Excel File
    ↓
📥 Import Orders
    ↓ (creates Order + OrderAddress)
Shippo_Order::create()
    ↓ (gets available rates)
📋 Show Rates (3-5 options)
    ↓ (user selects one)
Shippo_Transaction::create()
    ↓ (generates label)
✅ Tracking Number + Label PDF
    ↓ (saved in database)
OrderTransaction record
```

---

## 💾 Where Data is Stored

### Orders Table
```
id | order_number | shipping_name | status    | created_at
---|--------------|---------------|-----------|---
1  | ORD-001      | John Smith    | pending   | 2024-05-09
2  | ORD-002      | Jane Doe      | shipped   | 2024-05-09
```

### Order Transactions (Labels)
```
id | order_id | tracking_number   | label_url                   | status
---|----------|-------------------|---------------------------|--------
1  | 1        | 1234567890123     | https://shippo-prod.s3...  | SUCCESS
2  | 2        | 9876543210987     | https://shippo-prod.s3...  | SUCCESS
```

---

## 🔗 Useful Links

- **Staff Dashboard**: http://localhost:8000/staff/dashboard
- **Orders**: http://localhost:8000/staff/orders
- **Shippo API Docs**: https://goshippo.com/docs/reference/
- **USPS Tracking**: https://tools.usps.com/go/TrackConfirmAction_input
- **UPS Tracking**: https://www.ups.com/track

---

## 📞 Support

### Get Help
- Check logs: `storage/logs/laravel.log`
- Email: support@phoenix.local
- Slack: #shippo-support

### Debug Info Needed
1. Order number: `ORD-001`
2. Error message shown
3. Time it happened: `2024-05-09 10:30 AM`

---

## ✅ Checklist Before Shipping

- [ ] Order number is correct
- [ ] Customer name matches order
- [ ] Address is complete and valid
- [ ] Package weight is accurate
- [ ] Selected a shipping rate
- [ ] Downloaded label PDF
- [ ] Label is attached to package
- [ ] Tracking number noted
- [ ] Package handed off to carrier

---

## 🎯 Best Practices

1. **Get Rates Early**
   - Don't wait - rates expire after ~5 minutes
   - Re-fetch if you wait too long

2. **Use Accurate Weights**
   - Carrier charges by actual weight
   - Round UP to nearest 0.5 lb
   - Example: 2.3 lb → 2.5 lb

3. **Download Labels Immediately**
   - PDFs expire in 7 days
   - Store in order archive
   - Print same day if possible

4. **Track All Shipments**
   - Use tracking number in confirmation email
   - Monitor for delivery issues
   - Update customer when delivered

5. **International Orders**
   - Check address carefully
   - Phone number required
   - Expect higher shipping costs
   - Longer delivery times (5-7 days)

---

## 📈 Performance Stats

### Typical Processing Times
- Import 10 orders: ~5 seconds ✓
- Get rates (1 order): ~3 seconds ✓
- Create label: ~5 seconds ✓
- **Total**: ~13 seconds per order

### Carriers Available
| US Domestic | International |
|-------------|---------------|
| USPS ✓      | DHL ✓         |
| UPS ✓       | FedEx ✓       |
| FedEx ✓     | DPD ✓         |
| Amazon ✓    | Asendia ✓     |

---

## 🔐 Security Notes

- ✅ Only staff can import orders
- ✅ API key is encrypted
- ✅ Labels expire automatically
- ✅ Tracking data is logged
- ✅ All operations are audited

---

## 📝 Field Requirements

### Required Fields
- `order_number` (max 255 chars)
- `shipping_name` (max 255 chars)
- `shipping_street` (max 255 chars)
- `shipping_city` (max 255 chars)
- `shipping_zip` (max 20 chars)
- `shipping_province` (max 255 chars)
- `shipping_country` (2-letter code: US, GB, CA, etc.)
- `lineitem_quantity` (integer ≥ 1)
- `lineitem_name` (max 255 chars)

### Optional Fields
- `created_at` (YYYY-MM-DD format)
- `shipping_address1` (max 255 chars)
- `shipping_address2` (max 255 chars)
- `shipping_company` (max 255 chars)
- `lineitem_price` (numeric)

---

## 🎓 Training Tips

### For New Staff
1. Start with US addresses only
2. Use USPS first (most reliable)
3. Small packages first (<5 lbs)
4. Learn error messages by testing

### Common Questions
**Q**: How long does delivery take?
**A**: Check rate details - usually 1-5 days for US

**Q**: Can I change label after purchase?
**A**: No - create new label if needed (old one still valid)

**Q**: What if package is lost?
**A**: Use tracking number for claim with carrier

**Q**: Can I refund a label?
**A**: Yes - contact Shippo support within 30 days

