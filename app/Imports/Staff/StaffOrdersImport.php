<?php

namespace App\Imports\Staff;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

use Illuminate\Support\Facades\Validator;

class StaffOrdersImport implements ToCollection, WithHeadingRow
{
    public $rows = [];
    public $addresses = [];
    public $errors = [];
    public $products = [];
    public $userId;

    public function __construct($id)
    {
        $this->userId = $id;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $rowCollection) {
            if($rowCollection->filter()->isNotEmpty()){
                $row = $rowCollection->toArray();

                // TODO check condition validate
                $validator = Validator::make($row, [
                    'created_at' => 'nullable|date',
                    'order_number' => 'nullable|max:255',

                    'shipping_name' => 'required|string|max:255',
                    'shipping_street' => 'required|string|max:255',
                    'shipping_address1' => 'nullable|string|max:255',
                    'shipping_address2' => 'nullable|string|max:255',
                    'shipping_company' => 'nullable|string|max:255',
                    'shipping_city' => 'required|string|max:255',
                    'shipping_zip' => 'required',
                    'shipping_province' => 'required|string|max:255',
                    'shipping_country' => 'required|string|max:255',
                    // 'shipping_phone' => 'nullable|string|max:255',

                    'lineitem_quantity' => 'required|integer|min:1',
                    'lineitem_name' => 'required|exists:products,name,deleted_at,NULL,user_id,' . $this->userId,
                    'lineitem_price' => 'nullable|numeric|min:0|not_in:0',
                    'lineitem_compare_at_price' => 'nullable|numeric|min:0|not_in:0',
                    'lineitem_sku' => 'required|string|max:255',
                    'lineitem_requires_shipping' => 'nullable|integer|min:0',
                    'lineitem_taxable' => 'nullable|numeric|min:0|not_in:0',
                    'lineitem_fulfillment_status' => 'nullable|string|max:255',
                ]);

                if ($validator->fails()) {
                    $this->errors[$key] = $validator->errors()->first();
                    continue;
                }

                $street = $row['shipping_street'];
                if ($row['shipping_address1']) {
                    $street .= ',' . $row['shipping_address1'];
                }

                if ($row['shipping_address2']) {
                    $street .= ',' . $row['shipping_address2'];
                }

                $address = [
                    'name' => $row['shipping_name'],
                    'company' => $row['shipping_company'] ?? null,
                    //'street1' => $row['shipping_street'],
                    'street1' => $street,
                    'street2' => $row['shipping_address1'] ?? null,
                    'street3' => $row['shipping_address2'] ?? null,
                    'city' => $row['shipping_city'],
                    'state' => $row['shipping_province'],
                    'zip' => $row['shipping_zip'],
                    'country' => $row['shipping_country'],
                    'phone' => $row['shipping_phone'] ?? null,
                ];

                $validateAddress = OrderAddress::validateAddress($address);
                if (count($validateAddress['errorMsg'])) {
                    $this->errors[$key] = $validateAddress['errorMsg'][0];
                    continue;
                }

                $address['street1'] = $row['shipping_street'];
                $address['object_id'] = $validateAddress['value']['object_id'];
                $address['user_id'] = $this->userId;
                $this->addresses[$key] = $address;

                if (!array_key_exists($row['lineitem_name'], $this->products)) {
                    $product = Product::with('packageGroupWithTrashed')
                        ->where('name', $row['lineitem_name'])
                        ->where('user_id', $this->userId)
                        ->first();

                    $this->products[$row['lineitem_name']] = $product;
                }

                if (!$this->products[$row['lineitem_name']]) {
                    $this->errors[$key] = "The selected lineitem name is invalid.";
                    continue;
                }

                $validator = Validator::make($row, [
                    'lineitem_sku' => 'exists:inventories,sku,deleted_at,NULL,product_id,' . $this->products[$row['lineitem_name']]->id
                ]);

                if ($validator->fails()) {
                    $this->errors[$key] = $validator->errors()->first();
                }

                $this->rows[$key] = $row;
            }
        }
    }
}
