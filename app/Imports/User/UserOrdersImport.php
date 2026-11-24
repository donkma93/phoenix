<?php

namespace App\Imports\User;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

use Illuminate\Support\Facades\Validator;

class UserOrdersImport implements ToCollection, WithHeadingRow
{
    public $rows = [];
    public $addresses = [];
    public $errors = [];
    public $products = [];

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $userId = Auth::id();

        // Pre-collect all distinct product names in the CSV to preload products & inventory SKUs in 2 queries total
        $neededNames = [];
        foreach ($collection as $rowCollection) {
            if ($rowCollection->filter()->isNotEmpty()) {
                $name = $rowCollection['lineitem_name'] ?? null;
                if ($name !== null && $name !== '') {
                    $neededNames[$name] = true;
                }
            }
        }

        if (!empty($neededNames)) {
            $preloadedProducts = Product::with('packageGroupWithTrashed')
                ->where('user_id', $userId)
                ->whereIn('name', array_keys($neededNames))
                ->get();

            foreach ($preloadedProducts as $prod) {
                $this->products[$prod->name] = $prod;
            }
        }

        $productIds = [];
        foreach ($this->products as $prod) {
            if ($prod) {
                $productIds[] = $prod->id;
            }
        }

        // Preload valid (product_id => [sku => true]) from inventories in 1 query (eliminates N+1 Validator exists queries)
        $validSkusByProduct = [];
        if (!empty($productIds)) {
            $invRows = DB::table('inventories')
                ->select('product_id', 'sku')
                ->whereIn('product_id', $productIds)
                ->whereNull('deleted_at')
                ->get();
            foreach ($invRows as $inv) {
                $validSkusByProduct[$inv->product_id][(string) $inv->sku] = true;
            }
        }

        // Memoize Shippo address validation results within this import (avoids duplicate HTTP API calls for same address)
        $validatedAddressCache = [];

        foreach ($collection as $key => $rowCollection) {
            if($rowCollection->filter()->isNotEmpty()){
                // TODO check condition validate
                $row = $rowCollection->toArray();

                $validator = Validator::make($row, [
                    'created_at' => 'nullable|date',
                    'order_number' => 'nullable|max:255',

                    'shipping_name' => 'required|max:255',
                    'shipping_street' => 'required|max:255',
                    'shipping_address1' => 'nullable|max:255',
                    'shipping_address2' => 'nullable|max:255',
                    'shipping_company' => 'nullable|max:255',
                    'shipping_city' => 'required|max:255',
                    'shipping_zip' => 'required|max:20',
                    'shipping_province' => 'required|max:255',
                    'shipping_country' => 'required|max:255',
                    // 'shipping_phone' => 'nullable|max:255',

                    'lineitem_quantity' => 'required|integer|min:1',
                    'lineitem_name' => 'required|max:255',
                    'lineitem_price' => 'nullable|numeric|min:0|not_in:0',
                    'lineitem_compare_at_price' => 'nullable|numeric|min:0|not_in:0',
                    'lineitem_sku' => 'required|max:255',
                    'lineitem_requires_shipping' => 'nullable|integer|min:0',
                    'lineitem_taxable' => 'nullable|numeric|min:0|not_in:0',
                    'lineitem_fulfillment_status' => 'nullable|max:255',

                    // 'lineitem_discount' => '',
                ]);

                if ($validator->fails()) {
                    $this->errors[$key] = $validator->errors()->first();
                    continue;
                }

                // Check product & SKU in-memory BEFORE making external Shippo HTTP call
                $productName = $row['lineitem_name'];
                $product = $this->products[$productName] ?? null;
                if (!$product) {
                    $this->errors[$key] = "The selected lineitem name is invalid.";
                    continue;
                }

                if (!isset($validSkusByProduct[$product->id][(string) $row['lineitem_sku']])) {
                    $this->errors[$key] = "The selected lineitem sku is invalid.";
                    continue;
                }

                $street = $row['shipping_street'];
                if ($row['shipping_address1']) {
                    $street .= ',' . $row['shipping_address1'];
                }

                if (isset($row['shipping_address2']) && !empty($row['shipping_address2'])) {
                    $street .= ',' . $row['shipping_address2'];
                }

                $address = [
                    'name' => $row['shipping_name'],
                    'company' => $row['shipping_company'] ?? null,
                    'street1' => $street,
                    'street2' => $row['shipping_address1'] ?? null,
                    'street3' => $row['shipping_address2'] ?? null,
                    'city' => $row['shipping_city'],
                    'state' => $row['shipping_province'],
                    'zip' => $row['shipping_zip'],
                    'country' => $row['shipping_country'],
                    'phone' => $row['shipping_phone'] ?? null,
                ];

                $addrHash = md5(json_encode($address));
                if (!array_key_exists($addrHash, $validatedAddressCache)) {
                    $validatedAddressCache[$addrHash] = OrderAddress::validateAddress($address);
                }
                $validateAddress = $validatedAddressCache[$addrHash];

                if (count($validateAddress['errorMsg'])) {
                    $this->errors[$key] = $validateAddress['errorMsg'][0];
                    continue;
                }

                $address['street1'] = $row['shipping_street'];
                $address['object_id'] = $validateAddress['value']['object_id'];
                $address['user_id'] = $userId;
                $this->addresses[$key] = $address;

                $this->rows[$key] = $row;
            }
        }
    }
}
