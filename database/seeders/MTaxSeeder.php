<?php

namespace Database\Seeders;

use App\Models\MTax;
use Illuminate\Database\Seeder;

class MTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MTax::create([
            'tax' => 12
        ]);
    }
}
