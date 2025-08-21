<?php

namespace Database\Seeders;

use App\Models\MRequestType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MRequestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        MRequestType::insert([
            [
                'name' => 'relabel',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'repack',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'outbound',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'add package',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'removal',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'return',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'warehouse labor',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
