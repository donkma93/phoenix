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

class StaffLabelsImport implements ToCollection, WithHeadingRow
{
    private $sheet = 0;
    public $rows = [];
    public $errors = [];
    //public $addresses = [];

    public function __construct()
    {
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $this->sheet += 1;
        if ($this->sheet == 1) {
            foreach ($collection as $key => $rowCollection) {
                if ($rowCollection->filter()->isNotEmpty()) {
                    $row = $rowCollection->toArray();

                    // TODO check condition validate
                    $validator = Validator::make($row, [
                        'order_id' => 'required|integer',
                        'shipping_name' => 'required|string',
                        'shipping_country' => 'required|alpha_dash',
                        'shipping_province' => 'required|alpha_dash',
                        'shipping_city' => 'required|string',
                        'shipping_street' => 'required|string',
                        'shipping_zip' => 'required',
                        'package_length' => 'nullable|numeric',
                        'package_width' => 'nullable|numeric',
                        'package_height' => 'nullable|numeric',
                        'package_weight' => 'nullable|numeric',
                        'size_type' => 'nullable|numeric',
                        'weight_type' => 'nullable|numeric',
                        'shipping_phone' => 'nullable',
                        'shipping_company' => 'nullable',
                        'shipping_address1' => 'nullable',
                        'shipping_address2' => 'nullable',
                    ]);

                    if ($validator->fails()) {
                        $this->errors[$key] = $validator->errors()->first();
                        continue;
                    }

                    /*$address = [
                        'name' => $row['shipping_name'],
                        'company' => $row['shipping_company'] ?? null,
                        'street1' => $row['shipping_street'],
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

                    $address['object_id'] = $validateAddress['value']['object_id'];
                    $this->addresses[$key] = $address;*/

                    $this->rows[$key] = $row;
                }
            }
        }
    }
}
