<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MRequestType;
use App\Models\UnitPrice;
use Carbon\Carbon;

class UnitPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $requests = MRequestType::get()->mapWithKeys(function ($item) {
            return [$item['name'] => $item['id']];
        });

        $now = Carbon::now();

        UnitPrice::insert([
            // Inbound
            [
                'm_request_type_id' => $requests["add package"],
                'min_unit' => null,
                'max_unit' => null,
                'min_size_price' => 1,
                'max_size_price' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Outbound
            [
                'm_request_type_id' => $requests["outbound"],
                'min_unit' => null,
                'max_unit' => null,
                'min_size_price' => 1.5,
                'max_size_price' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'm_request_type_id' => $requests["warehouse labor"],
                'min_unit' => null,
                'max_unit' => null,
                'min_size_price' => 40,
                'max_size_price' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'm_request_type_id' => $requests["relabel"],
                'min_unit' => 1,
                'max_unit' => 499,
                'min_size_price' => 0.5,
                // todo max size
                'max_size_price' => 0.6,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'm_request_type_id' => $requests["relabel"],
                'min_unit' => 500,
                'max_unit' => 1000,
                'min_size_price' => 0.45,
                // todo max size
                'max_size_price' => 0.55,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'm_request_type_id' => $requests["relabel"],
                'min_unit' => 1001,
                'max_unit' => null,
                'min_size_price' => 0.4,
                // todo max size
                'max_size_price' => 0.5,
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'm_request_type_id' => $requests["return"],
                'min_unit' => null,
                'max_unit' => null,
                'min_size_price' => 0.5,
                'max_size_price' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'm_request_type_id' => $requests["repack"],
                'min_unit' => null,
                'max_unit' => null,
                // $0.5/unit or $3/box
                'min_size_price' => 0.5,
                'max_size_price' => 3,
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'm_request_type_id' => $requests["removal"],
                'min_unit' => 1,
                'max_unit' => 499,
                'min_size_price' => 0.2,
                // todo max size
                'max_size_price' => 0.3,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'm_request_type_id' => $requests["removal"],
                'min_unit' => 500,
                'max_unit' => 1000,
                'min_size_price' => 0.15,
                // todo max size
                'max_size_price' => 0.25,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'm_request_type_id' => $requests["removal"],
                'min_unit' => 1001,
                'max_unit' => null,
                'min_size_price' => 0.1,
                // todo max size
                'max_size_price' => 0.2,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
