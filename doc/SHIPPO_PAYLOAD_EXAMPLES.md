# Shippo API - Detailed Payload Examples

## Table of Contents
1. [Shippo_Order Payload - US Address](#shippo_order-payload---us-address)
2. [Shippo_Order Payload - International Address](#shippo_order-payload---international-address)
3. [Transaction Request/Response](#transaction-requestresponse)
4. [Error Responses](#error-responses)
5. [Database Records](#database-records)

---

## Shippo_Order Payload - US Address

### Request (Input)

**Source File**: `app/Services/Staff/StaffOrderService.php` (line ~1750-1800)

```php
$payload = [
    "total_tax" => "0.00",
    
    // Source Address (Warehouse)
    "from_address" => [
        "object_purpose" => "PURCHASE",
        "name" => "Phoenix Warehouse",
        "company" => "Phoenix Logistics",
        "street1" => "123 Warehouse Ave",
        "city" => "Los Angeles",
        "state" => "CA",
        "zip" => "90001",
        "country" => "US"
    ],
    
    // Destination Address (Customer)
    "to_address" => [
        "object_purpose" => "PURCHASE",
        "name" => "John Smith",
        "company" => "Acme Inc",
        "street1" => "456 Main Street",
        "street2" => "Apt 4B",
        "city" => "New York",
        "state" => "NY",
        "zip" => "10001",
        "country" => "US",
        "phone" => "+1-555-0123"
    ],
    
    // Package Details
    "weight" => 2.5,
    "weight_unit" => "lb",
    
    // Order Details
    "order_number" => "ORD-001",
    "order_status" => "PAID",
    "placed_at" => "2024-05-09T00:00:00Z",
    
    // Line Items
    "items" => [
        [
            "title" => "iPhone 13",
            "total_amount" => 2,
            "weight_unit" => "lb"
        ]
    ],
    
    // Pricing
    "subtotal_price" => "0",
    "total_price" => "0",
    "shipping_cost" => null,
    "shipping_cost_currency" => "USD",
    "currency" => "USD",
    
    // Metadata
    "shop_app" => "Shippo",
    "shipping_method" => null,
    "hidden" => false
];
```

**As JSON**:
```json
{
    "total_tax": "0.00",
    "from_address": {
        "object_purpose": "PURCHASE",
        "name": "Phoenix Warehouse",
        "company": "Phoenix Logistics",
        "street1": "123 Warehouse Ave",
        "city": "Los Angeles",
        "state": "CA",
        "zip": "90001",
        "country": "US"
    },
    "to_address": {
        "object_purpose": "PURCHASE",
        "name": "John Smith",
        "company": "Acme Inc",
        "street1": "456 Main Street",
        "street2": "Apt 4B",
        "city": "New York",
        "state": "NY",
        "zip": "10001",
        "country": "US",
        "phone": "+1-555-0123"
    },
    "weight": 2.5,
    "weight_unit": "lb",
    "order_number": "ORD-001",
    "order_status": "PAID",
    "placed_at": "2024-05-09T00:00:00Z",
    "items": [
        {
            "title": "iPhone 13",
            "total_amount": 2,
            "weight_unit": "lb"
        }
    ],
    "subtotal_price": "0",
    "total_price": "0",
    "shipping_cost": null,
    "shipping_cost_currency": "USD",
    "currency": "USD",
    "shop_app": "Shippo",
    "shipping_method": null,
    "hidden": false
}
```

### Response (Output) - US Address

```json
{
    "object_id": "order_xxxxxxxxxxxxxxxxxxxxxxxx",
    "object_owner": "shippo",
    "object_state": "VALID",
    "object_created": "2024-05-09T10:30:00.123456Z",
    "object_updated": "2024-05-09T10:30:00.123456Z",
    "object_purpose": "PURCHASE",
    
    "address_from": {
        "object_id": "adr_abc123def456",
        "object_owner": "shippo",
        "object_created": "2024-05-09T10:30:00Z",
        "name": "Phoenix Warehouse",
        "company": "Phoenix Logistics",
        "street1": "123 Warehouse Ave",
        "street2": "",
        "street3": "",
        "street_no": "",
        "city": "Los Angeles",
        "state": "CA",
        "zip": "90001",
        "country": "US",
        "longitude": -118.2437,
        "latitude": 34.0522,
        "phone": "",
        "email": "",
        "is_residential": false,
        "test": false,
        "validation_results": {
            "is_valid": true,
            "messages": []
        }
    },
    
    "address_to": {
        "object_id": "adr_xyz789uvw012",
        "object_owner": "shippo",
        "object_created": "2024-05-09T10:30:00Z",
        "name": "John Smith",
        "company": "Acme Inc",
        "street1": "456 Main Street",
        "street2": "Apt 4B",
        "street3": "",
        "city": "New York",
        "state": "NY",
        "zip": "10001",
        "country": "US",
        "phone": "+1-555-0123",
        "email": "",
        "is_residential": true,
        "validation_results": {
            "is_valid": true,
            "messages": []
        }
    },
    
    "weight": "2.5",
    "weight_unit": "lb",
    
    "order_number": "ORD-001",
    "order_status": "PAID",
    "placed_at": "2024-05-09T00:00:00Z",
    
    "line_items": [
        {
            "object_id": "item_abc123",
            "title": "iPhone 13",
            "quantity": 2,
            "total_price": "0.00",
            "currency": "USD",
            "weight": "",
            "weight_unit": "lb"
        }
    ],
    
    "total_tax": "0.00",
    "total_price": "0.00",
    "currency": "USD",
    "subtotal_price": "0.00",
    "shipping_cost": null,
    "shipping_cost_currency": "USD",
    
    "shop_app": "Shippo",
    "shipping_method": null,
    "hidden": false,
    
    "rates": [
        {
            "object_id": "rate_xxx1",
            "object_owner": "shippo",
            "provider": "USPS",
            "servicelevel": {
                "token": "usps_ground",
                "name": "USPS Ground Advantage Mail",
                "terms": ""
            },
            "amount": "5.50",
            "currency": "USD",
            "max_delivery_time": 3,
            "estimated_days": 3,
            "arrives_by": "2024-05-12T23:59:00Z",
            "duration_terms": "Estimated",
            "test": false,
            "messages": []
        },
        {
            "object_id": "rate_xxx2",
            "object_owner": "shippo",
            "provider": "USPS",
            "servicelevel": {
                "token": "usps_priority",
                "name": "USPS Priority Mail",
                "terms": ""
            },
            "amount": "12.50",
            "currency": "USD",
            "max_delivery_time": 1,
            "estimated_days": 1,
            "arrives_by": "2024-05-10T23:59:00Z",
            "duration_terms": "Guaranteed",
            "test": false,
            "messages": []
        },
        {
            "object_id": "rate_xxx3",
            "object_owner": "shippo",
            "provider": "UPS",
            "servicelevel": {
                "token": "ups_ground",
                "name": "UPS Ground",
                "terms": ""
            },
            "amount": "18.75",
            "currency": "USD",
            "max_delivery_time": 5,
            "estimated_days": 5,
            "arrives_by": "2024-05-14T23:59:00Z",
            "duration_terms": "Estimated",
            "test": false,
            "messages": []
        }
    ],
    
    "messages": [],
    "test": false
}
```

---

## Shippo_Order Payload - International Address

### Request (Input)

**Difference**: When country is NOT "US", uses PURCHASE object_purpose

```json
{
    "total_tax": "0.00",
    
    "from_address": {
        "object_purpose": "PURCHASE",
        "name": "Phoenix Warehouse",
        "company": "Phoenix Logistics",
        "street1": "123 Warehouse Ave",
        "city": "Los Angeles",
        "state": "CA",
        "zip": "90001",
        "country": "US"
    },
    
    "to_address": {
        "object_purpose": "PURCHASE",
        "name": "Jane Doe",
        "company": "Global Trading Ltd",
        "street1": "789 Oxford Street",
        "street2": "Suite 500",
        "city": "London",
        "state": "London",
        "zip": "SW1A 1AA",
        "country": "GB",
        "phone": "+44-20-7946-0958"
    },
    
    "weight": 5.0,
    "weight_unit": "lb",
    
    "order_number": "ORD-INT-001",
    "order_status": "PAID",
    "placed_at": "2024-05-09T00:00:00Z",
    
    "items": [
        {
            "title": "MacBook Pro",
            "total_amount": 1,
            "weight_unit": "lb"
        },
        {
            "title": "Magic Keyboard",
            "total_amount": 1,
            "weight_unit": "lb"
        }
    ],
    
    "subtotal_price": "0",
    "total_price": "0",
    "currency": "USD",
    "shop_app": "Shippo",
    "hidden": false
}
```

### Response (Output) - International Address

```json
{
    "object_id": "order_yyyyyyyyyyyyyyyyyyyyyyyy",
    "object_owner": "shippo",
    "object_state": "VALID",
    "rates": [
        {
            "object_id": "rate_int_1",
            "provider": "DHL",
            "servicelevel": {
                "token": "dhl_express",
                "name": "DHL Express"
            },
            "amount": "65.00",
            "currency": "USD",
            "estimated_days": 2,
            "arrives_by": "2024-05-11T23:59:00Z"
        },
        {
            "object_id": "rate_int_2",
            "provider": "FedEx",
            "servicelevel": {
                "token": "fedex_international_priority",
                "name": "FedEx International Priority"
            },
            "amount": "78.50",
            "currency": "USD",
            "estimated_days": 3,
            "arrives_by": "2024-05-12T23:59:00Z"
        },
        {
            "object_id": "rate_int_3",
            "provider": "DPD",
            "servicelevel": {
                "token": "dpd_express",
                "name": "DPD Express"
            },
            "amount": "45.00",
            "currency": "USD",
            "estimated_days": 4,
            "arrives_by": "2024-05-13T23:59:00Z"
        }
    ],
    "messages": [],
    "test": false
}
```

---

## Transaction Request/Response

### Transaction Request

**Source**: `app/Models/OrderTransaction.php::createTransaction()` (line ~45)

**Payload**:
```json
{
    "rate": "rate_xxx2",
    "order_id": "order_xxxxxxxxxxxxxxxxxxxxxxxx",
    "label_file_type": "PDF_4x6",
    "async": false
}
```

### Transaction Response - SUCCESS

```json
{
    "object_id": "txn_aaaaaaaaaaaaaaaaaaaa",
    "object_owner": "shippo",
    "object_state": "VALID",
    "status": "SUCCESS",
    
    "rate": {
        "object_id": "rate_xxx2",
        "provider": "USPS",
        "servicelevel": {
            "token": "usps_priority",
            "name": "Priority Mail"
        },
        "amount": "12.50",
        "currency": "USD"
    },
    
    "order_id": "order_xxxxxxxxxxxxxxxxxxxxxxxx",
    
    "tracking_number": "1234567890123",
    "tracking_status": "TRANSIT",
    "tracking_status_details": {
        "status": "in_transit",
        "status_detail": "Out for Delivery",
        "status_date": "2024-05-09T14:30:00Z"
    },
    
    "tracking_url_provider": "https://tools.usps.com/go/TrackConfirmAction_input?tLabels=1234567890123",
    
    "label_download": {
        "href": "https://shippo-prod.s3.amazonaws.com/label_2024_05_09_abc123.pdf",
        "expiresAt": "2024-05-16T10:30:00Z"
    },
    
    "commercial_invoice_download": null,
    "manifest_download": null,
    "return_label_download": null,
    
    "submitted_datetime": "2024-05-09T10:35:00Z",
    "eta_datetime": "2024-05-10T18:00:00Z",
    
    "test": false,
    "messages": [],
    
    "metadata": ""
}
```

### Transaction Response - ERROR

```json
{
    "object_id": "txn_error123",
    "status": "ERROR",
    "messages": [
        {
            "code": "INVALID_RATE",
            "message": "The selected rate is no longer available"
        },
        {
            "code": "ADDRESS_ERROR",
            "message": "Invalid recipient address"
        }
    ]
}
```

---

## Error Responses

### Error 1: Invalid Address Format

```json
{
    "status": "error",
    "errors": [
        "Recipient address must include city and postal code"
    ]
}
```

### Error 2: Rate Not Found

```json
{
    "status": "error",
    "errors": [
        "Rate not found or has expired"
    ]
}
```

### Error 3: Weight Too Heavy

```json
{
    "status": "error",
    "errors": [
        "Package weight exceeds maximum allowed weight for this service"
    ]
}
```

### Error 4: Unsupported Country Pair

```json
{
    "status": "error",
    "errors": [
        "Shipping from US to this destination is not currently available"
    ]
}
```

---

## Database Records

### Orders Table After Import

```sql
INSERT INTO orders (
    id, user_id, order_number, order_status,
    shipping_name, shipping_street, shipping_address1, shipping_address2,
    shipping_company, shipping_city, shipping_zip, shipping_province, shipping_country,
    created_at, updated_at
)
VALUES (
    123, 1, 'ORD-001', 'pending',
    'John Smith', '456 Main Street', 'Apt 4B', '',
    'Acme Inc', 'New York', '10001', 'NY', 'US',
    '2024-05-09 10:00:00', '2024-05-09 10:00:00'
);

-- Result
id | user_id | order_number | order_status | shipping_name | ... | created_at
---|---------|--------------|--------------|---------------|-----|---
123| 1       | ORD-001      | pending      | John Smith    | ... | 2024-05-09 10:00:00
```

### Order Addresses Table

```sql
INSERT INTO order_addresses (
    id, order_id, type, name, street1, street2, city, state, zip, country
)
VALUES
(
    789, 123, 'to', 'John Smith', '456 Main Street', 'Apt 4B',
    'New York', 'NY', '10001', 'US'
),
(
    790, 123, 'from', 'Phoenix Warehouse', '123 Warehouse Ave', '',
    'Los Angeles', 'CA', '90001', 'US'
);
```

### Order Rates Table (After storeLabel)

```sql
INSERT INTO order_rates (
    id, order_id, rate_object_id, provider, service_level,
    cost, currency, estimated_days, object_owner
)
VALUES
(
    456, 123, 'rate_xxx1', 'USPS', 'Ground Advantage',
    '5.50', 'USD', 3, 'shippo'
),
(
    457, 123, 'rate_xxx2', 'USPS', 'Priority Mail',
    '12.50', 'USD', 1, 'shippo'
),
(
    458, 123, 'rate_xxx3', 'UPS', 'Ground',
    '18.75', 'USD', 5, 'shippo'
);
```

### Order Transactions Table (After storeRate)

```sql
INSERT INTO order_transactions (
    id, order_id, order_rate_id, transaction_id,
    label_url, tracking_number, tracking_status, tracking_url_provider,
    created_at, updated_at
)
VALUES (
    101, 123, 457, 'txn_aaaaaaaaaaaaaaaaaaaa',
    'https://shippo-prod.s3.amazonaws.com/label_2024_05_09_abc123.pdf',
    '1234567890123', 'TRANSIT',
    'https://tools.usps.com/go/TrackConfirmAction_input?tLabels=1234567890123',
    '2024-05-09 10:35:00', '2024-05-09 10:35:00'
);

-- Result
id  | order_id | tracking_number | label_url              | tracking_status
----|----------|-----------------|------------------------|----------------
101 | 123      | 1234567890123   | https://shippo-prod... | TRANSIT
```

### Order Products Table

```sql
INSERT INTO order_products (
    id, order_id, product_id, quantity, price
)
VALUES (
    501, 123, 1, 2, 999.99
);
```

### Order Packages Table

```sql
INSERT INTO order_packages (
    id, order_id, length, width, height, weight, weight_type,
    distance_unit, created_at
)
VALUES (
    201, 123, 10, 8, 5, 2.5, 'lb', 'in', '2024-05-09 10:20:00'
);
```

---

## Complete Test Data SQL

```sql
-- Create test staff user
INSERT INTO users (name, email, password, role, status, created_at)
VALUES ('Test Staff', 'staff@test.com', bcrypt('password'), 'staff', 'active', NOW());

-- Import test order
INSERT INTO orders (user_id, order_number, shipping_name, shipping_street, shipping_city, shipping_zip, shipping_province, shipping_country, created_at)
VALUES (1, 'TEST-001', 'John Smith', '456 Main St', 'New York', '10001', 'NY', 'US', NOW());

-- Query results
SELECT * FROM orders WHERE order_number = 'TEST-001';
SELECT * FROM order_addresses WHERE order_id = 123;
SELECT * FROM order_rates WHERE order_id = 123;
SELECT * FROM order_transactions WHERE order_id = 123;
```

