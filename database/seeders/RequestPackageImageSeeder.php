<?php

namespace Database\Seeders;

use App\Models\RequestPackageImage;
use Illuminate\Database\Seeder;

class RequestPackageImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RequestPackageImage::factory(300)->create();
    }
}
