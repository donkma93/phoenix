<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

use Illuminate\Support\Facades\Validator;

class ImportPricesExcel implements ToCollection, WithHeadingRow
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
        foreach ($collection as $key => $rowCollection) {
            if($rowCollection->filter()->isNotEmpty()){
                // TODO check condition validate
                $row = $rowCollection->toArray();

                $validator = Validator::make($row, [
                    'weight' => 'required|integer|min:0',
                    'price' => 'required|integer|min:0',
                    'destination' => 'required|min:2',
                ]);

                if ($validator->fails()) {
                    $this->errors[$key] = $validator->errors()->first();
                    break;
                }

                $this->rows[$key] = $row;
            }
        }
    }
}
