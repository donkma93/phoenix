<?php

namespace Database\Seeders;

use App\Models\PackageGroup;
use Illuminate\Database\Seeder;

class PackageGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PackageGroup::factory(50)->create();
    }
}
