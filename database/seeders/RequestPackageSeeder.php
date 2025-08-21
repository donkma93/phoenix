<?php

namespace Database\Seeders;

use App\Models\RequestPackage;
use Illuminate\Database\Seeder;

class RequestPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RequestPackage::factory(200)->create();
    }
}
