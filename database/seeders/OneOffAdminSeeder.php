<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class OneOffAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            [
                'email' => 'admin@phoenix.local',
            ],
            [
                'password' => Hash::make('secret123'),
                'role' => User::ROLE_ADMIN,
                'partner_id' => 0,
                'partner_code' => 'ADMIN',
                'email_verified_at' => now(),
            ]
        );
    }
}


