<!-- Excel Template for Shippo Order Import -->
<!-- Save as: shippo_orders_test_template.xlsx -->

Sample rows for testing:

Row 1 (Headers):
created_at | order_number | shipping_name | shipping_street | shipping_address1 | shipping_address2 | shipping_company | shipping_city | shipping_zip | shipping_province | shipping_country | lineitem_quantity | lineitem_name | lineitem_price

Row 2 (Test US Address):
2024-05-09 | TEST-001 | John Smith | 123 Main Street | Apt 4B | | Acme Inc | New York | 10001 | NY | US | 2 | iPhone 13 | 999.99

Row 3 (Test with Multiple Items):
2024-05-09 | TEST-002 | Jane Doe | 456 Oak Avenue | Suite 100 | Floor 2 | Tech Corp | Los Angeles | 90001 | CA | US | 1 | MacBook Pro | 1999.99

Row 4 (Test International Address):
2024-05-09 | TEST-003 | James Wilson | 789 Park Lane | Unit 5 | | Global Traders | London | SW1A 1AA | London | GB | 3 | iPad Air | 599.99

Row 5 (Test No Company):
2024-05-09 | TEST-004 | Maria Garcia | 321 Elm Street | | | | Chicago | 60601 | IL | US | 1 | Apple Watch | 399.99

INSTRUCTIONS:
1. Create Excel file with these columns in this exact order
2. Keep headers in Row 1
3. Data starts from Row 2
4. Don't leave required columns empty:
   - order_number
   - shipping_name
   - shipping_street
   - shipping_city
   - shipping_zip
   - shipping_province
   - shipping_country
   - lineitem_quantity
   - lineitem_name

5. Optional columns can be empty:
   - created_at (defaults to now())
   - shipping_address1
   - shipping_address2
   - shipping_company
   - lineitem_price

UPLOAD INSTRUCTIONS:
1. Login to Phoenix as STAFF user
2. Navigate to: http://localhost:8000/staff/orders
3. Click "Import Orders"
4. Select this Excel file
5. Verify data in the validation step
6. Proceed to create labels and rates
7. Select rates and generate tracking numbers

EXPECTED OUTPUTS:
- Order created in DB with status 'pending'
- OrderAddress records for from/to addresses
- OrderRate options displayed from Shippo
- OrderTransaction with tracking_number after selection
- Label URL downloadable as PDF
