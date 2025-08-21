<?php

namespace Database\Seeders;

use App\Models\WarehouseArea;
use Illuminate\Database\Seeder;

class WarehouseAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WarehouseArea::factory(20)->create();
    }
}
