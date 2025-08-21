<?php

namespace Database\Seeders;

use App\Models\StoragePrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;


class StoragePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        StoragePrice::insert([
            [
                "month" => 1,
                "price" => 0.6,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                "month" => 4,
                "price" => 0.9,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
