# Cấu trúc BUY LABELS VIA SHIPBAE (Gori Company)

## Tổng quan
Shipbae dùng Gori API v2 (`https://docs-dev.goricompany.com/api/documentation`).
Luồng tích hợp mirror Myib:

1. **Tạo label đơn lẻ** → lấy rates → chọn rate → mua shipment
2. **Import Excel hàng loạt** → chọn rate rẻ nhất (hoặc `service`/`package_type` trong Excel) → tạo label
3. **Webhook** cập nhật tracking

## Auth
- `POST /auth/token`
- Body: `client_id`, `client_secret`, `grant_type=client_credentials`
- Header sau đó: `Authorization: Bearer {access_token}`

## Config (`.env`)
```
SHIPBAE_BASE_URL=https://staging.api.goricompany.com/v2
SHIPBAE_CLIENT_ID=...
SHIPBAE_CLIENT_SECRET=...
SHIPBAE_TIMEOUT=60
```

Config path: `config('services.shipbae.*')`

Production URL: `https://api.goricompany.com/v2`

## Routes
- `POST staff/orders/labels/create-via-shipbae` → `orders.labels.create.shipbae`
- `POST staff/labels/import-excel-shipbae` → `labels.import.excel.shipbae`
- `POST /api/shipbae-webhook` → `webhook.shipbae`

## Discriminators
- `order_rates.object_owner = 'shipbae'`
- `order_rates.provider = 'Shipbae'`
- `order_transactions.shipping_provider = 'SHIPBAE'`

## API chính
- Rates: `POST /shipments/rates`
- Create label: `POST /shipments` (cần `service`)
- Track: `GET /shipments/{id}/track`
- Refund: `POST /shipments/{id}/refund`

Parcel:
- dimensions: **inches**
- weight: **ounces**

## Address / ZIP rules (US)
- `from_address.zip` / `to_address.zip` must be string `#####` or `#####-####`.
- Excel `shipping_zip` is normalized (trim, numeric cast, leading-zero pad) before create.
- **Rates can succeed even when create rejects ZIP.** Create `/shipments` is authoritative.
- Example: Portland `97275` is rejected by Shipbae create with `[from_address.zip] Zip Code is not valid`, while nearby ZIPs like `97214` / `97201` / `97266` succeed. Prefer a street-deliverable warehouse ZIP, not PO Box / unique ZIP if carrier rejects it.
- Excel Shipbae import always upserts sender `addressFrom` from the file so corrected ZIP takes effect on re-import.

## Files
- `app/Services/Shipbae/ShipbaeClient.php`
- `app/Services/Staff/StaffOrderService.php` (`storeLabelShipbae`, `storeExcelShipbae`, ...)
- `app/Http/Controllers/Staff/StaffOrderController.php`
- `app/Http/Controllers/WebhookShipbaeController.php`
- `resources/views/order/create_label.blade.php`
- `resources/views/order/import-create-label.blade.php`
