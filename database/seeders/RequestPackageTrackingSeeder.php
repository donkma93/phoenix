<?php

namespace Database\Seeders;

use App\Models\RequestPackageTracking;
use Illuminate\Database\Seeder;

class RequestPackageTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RequestPackageTracking::factory(300)->create();
    }
}
