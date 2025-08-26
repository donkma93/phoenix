# API Reference

All endpoints are relative to the server base URL. JSON is used for requests/responses unless stated.

Authentication
- Scheme: JWT (Bearer)
- Header: Authorization: Bearer <token>

## Auth

- POST /api/auth/login
  - Request: { "email": string, "password": string }
  - Response 200: { "access_token": string, "token_type": "bearer", "expires_in": number }

- POST /api/auth/logout
  - Headers: Authorization
  - Response 200: { "message": string }

- POST /api/auth/refresh
  - Headers: Authorization
  - Response 200: { "access_token": string, "token_type": "bearer", "expires_in": number }

## Orders (Protected)

- GET /api/orders/package
  - Query/Body: requires order context (see StaffOrderController@getOrderPackageApi)
  - Response 200: { "message_code": "SUCCESS", "package": { ... } }

- POST /api/orders/create-label
  - Body: { order_id: number, package_width: number, package_height: number, package_length: number, package_weight: number, size_type: number, weight_type: number, ... }
  - Response 200: { "message_code": "SUCCESS", "label_print_url": string, "tracking_code": string, "brand_delivery": string, "errorMsg": [] }

## Customer APIs (Protected)

Prefix: /api/customer

- POST /api/customer/order/create
  - Body: order + address fields (see UserOrderController@storeApi)
  - Response 200: { status: string, orderCode?: string, errors?: object }

- POST /api/customer/save-tracking-webhook-url
  - Body: { webhook_url: string }
  - Response 200: { status: string }

- POST /api/customer/get-order-detail
  - Body: { order_code: string }
  - Response 200: { status: string, data: { ... } }

- POST /api/customer/create-package-group
  - Body: { ... }
  - Response 200: { status: string }

## Pickup (Protected)

Prefix: /api/pickup

- PUT /api/pickup/{pickup_id}/start
  - Response 200: { status: string }

- PUT /api/pickup/scan
  - Body: { code: string, ... }
  - Response 200: { status: string, data?: object }

- PUT /api/pickup/{pickup_id}/finish
  - Response 200: { status: string }

- PUT /api/pickup/{pickup_id}/order-journeys
  - Response 200: { status: string, journeys: [ ... ] }

- GET /api/pickup/list
  - Response 200: { status: string, data: [ ... ] }

## Utilities (Protected)

Prefix: /api/util

- GET /api/util/packinglist/{packing_code}
  - Response 200: { status: string, data: { ... } }

- GET /api/util/bill/{bill_code}
  - Response 200: { status: string, data: { ... } }

## Packing List (Protected)

Prefix: /api/packing-list

- GET /api/packing-list/list
  - Response 200: { status: string, data: [ ... ] }

- POST /api/packing-list/store
  - Body: { ... }
  - Response 200: { status: string }

- PUT /api/packing-list/{picking_list_id}/start
  - Response 200: { status: string }

- PUT /api/packing-list/scan
  - Body: { code: string, ... }
  - Response 200: { status: string, data?: object }

- PUT /api/packing-list/finish
  - Body: { picking_list_id: number }
  - Response 200: { status: string }

- PUT /api/packing-list/receive-scan
  - Body: { code: string, ... }
  - Response 200: { status: string }

- PUT /api/packing-list/receive-finish
  - Body: { picking_list_id: number }
  - Response 200: { status: string }

- GET /api/packing-list/list-inbound
  - Response 200: { status: string, data: [ ... ] }

## v1 Tools (Protected)

Prefix: /api/v1

- POST /api/v1/check-tracking-exist
  - Body: { order_id: number }
  - Response 200: { status: "success", tracking_number: string } or { status: "error", tracking_number: null }

- POST /api/v1/update-tracking-info-by-order-id
  - Body: {
      order_id: number,
      tracking_number: string,
      shipping_carrier: string,
      tracking_status?: string,
      label_url?: string,
      amount?: number,
      currency?: string
    }
  - Response 200: { status: "success", message: string } or 400 error

- POST /api/v1/get-label-url-by-order-id
  - Body: { order_id: number }
  - Response 200: { status: "success", order_id: number, label_url: string }
  - Response 404: { status: "error", order_id: number, label_url: null, message: string }
  - Response 422: { status: "error", errors: object }

## Webhooks (Public)

- POST /api/webhook-shippo
  - Response 200: { status: string }

- POST /api/webhook-label-g7
  - Secured by custom middleware
  - Response 200: { status: string }

- POST /api/webhook-17track
  - Response 200: { status: string }

## Client (Public)

Prefix: /api/client

- GET /api/client/packinglist/{packing_code}
  - Response 200: { status: string, data: { ... } }

- GET /api/client/bill/{bill_code}
  - Response 200: { status: string, data: { ... } }

## Fallback

- ANY /api/{any}
  - Response 404: { status: "error", message: "Resource not found" }

Notes
- This is a concise map; see controllers for full payload schemas and validations.
- Protected endpoints require a valid JWT bearer token.
