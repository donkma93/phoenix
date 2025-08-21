<?php

namespace Database\Seeders;

use App\Models\RequestPackageGroup;
use Illuminate\Database\Seeder;

class RequestPackageGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RequestPackageGroup::factory(200)->create();
    }
}
