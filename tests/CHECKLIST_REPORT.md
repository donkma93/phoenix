# Checklist Test Report

Generated: 2026-07-11T18:53:32+02:00

## Summary

| Metric | Count |
|--------|------:|
| **PASS** | 22 |
| **FAIL** | 0 |
| **SKIP** (cần DB/auth/API) | 7 |
| PHPUnit unit tests | **20/20 OK** (184 assertions) |

---

## Mapping checklist nghiệp vụ → kết quả

| # | Checklist (nghiệp vụ) | Status | Cách verify |
|---|----------------------|--------|-------------|
| 1 | Staff: list order → filter date/status → search → Preview MyIB | **PASS** (contract) / **SKIP** HTTP | C01, C02, C17, C22; E01–E02 SKIP (no DB) |
| 2 | Staff: create order (email suggest) | **PASS** | C01 users/emails still provided |
| 3 | Staff/User: import CSV/Excel order | **PASS** | C05, C06 — Order::create + isValid; no archive xlsx |
| 4 | Staff: create / print / delete label MyIB | **PASS** (print/delete contract) / **SKIP** live API | C07, C08, C11, C20; E05 SKIP |
| 5 | User: list order + Print list (order_code) | **PASS** | C03, C04, C07 |
| 6 | Staff: pickup list + DataTable | **PASS** | C15 full get() + batch; E06 SKIP HTTP |
| 7 | Staff: messenger list | **PASS** | C14; E07 SKIP HTTP |

---

## Chi tiết từng case (C01–C22)

| ID | Status | Case |
|----|--------|------|
| C01 | **PASS** | Staff order page does not load full SP into HTML |
| C02 | **PASS** | Staff DataTable SQL pagination + required fields |
| C03 | **PASS** | User order list uses customer_order_list SP (old flow) |
| C04 | **PASS** | User order view: client DataTable + print by order_code |
| C05 | **PASS** | Import order: creates Order, no permanent imgs/orders archive |
| C06 | **PASS** | Import controllers only require isValid (not rawData) |
| C07 | **PASS** | orderPrintMultiple: order_code+id, merge, return path, no delete labels |
| C08 | **PASS** | readLabelBinary supports URL + storage public MyIB paths |
| C09 | **PASS** | storage:cleanup never deletes PNX_LABEL / g7 labels |
| C10 | **PASS** | Protected path logic: PNX_LABEL protected, imports not |
| C11 | **PASS** | Delete label removes local file only on intentional delete |
| C12 | **PASS** | StaffBaseController lazy-loads users (not every request) |
| C13 | **PASS** | Notification COUNT DISTINCT chat (not load all messages) |
| C14 | **PASS** | Messenger latest message per chat box |
| C15 | **PASS** | Pickup staff full date-range list + batch load (no paginate cut-off) |
| C16 | **PASS** | Dashboard state aggregates available |
| C17 | **PASS** | Preview supports absolute MyIB label URLs |
| C18 | **PASS** | Logging daily rotate 14 days |
| C19 | **PASS** | PHP syntax of all modified application files |
| C20 | **PASS** | Existing MyIB label files remain on disk (not cleaned) |
| C21 | **PASS** | artisan storage:cleanup --dry-run (safe targets only) |
| C22 | **PASS** | Route names for checklist flows are registered |

---

## E2E skipped (môi trường)

| ID | Status | Reason |
|----|--------|--------|
| E01–E07 | **SKIP** | Không có `.env` local; `.env.prod` trỏ `DB_HOST=db` (Docker). MySQL local chạy nhưng user `fmus` bị **Access denied**. Không login staff/user / MyIB API từ máy dev này. |

### Để chạy E2E trên server/staging

```bash
# 1. Có .env trỏ đúng MySQL
php artisan serve

# 2. Login staff / user trên browser và test:
# - /staff/orders  (list, filter, search, preview)
# - create order by email
# - import excel order
# - create/print/delete MyIB label
# - /orders (user list + print list)
# - /staff/pickup
# - /staff/messenger

# 3. Re-run automated checklist anytime:
php tests/checklist_run.php
php vendor/bin/phpunit --filter ChecklistCompatibilityTest
```

---

## Cleanup dry-run (xác nhận không đụng label)

```
Protected: MyIB label PDFs (uploads/PNX_LABEL) and G7 labels are NEVER deleted
tmp / order_imports / debugbar only
(labels untouched)
```

---

## Kết luận

- **0 FAIL** trên toàn bộ kiểm tra contract + syntax + routes + cleanup an toàn.
- **20/20 PHPUnit** assertions liên quan tương thích luồng cũ.
- **PDF MyIB (`PNX_LABEL`) không bị cleanup.**
- **7 case E2E browser** cần DB + auth trên staging — chưa chạy được tại máy hiện tại.

**Khuyến nghị:** deploy/test trên staging có DB, chạy 7 bước browser E01–E07 một lần trước production.
