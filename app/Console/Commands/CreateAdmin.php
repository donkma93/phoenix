<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {email} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update an admin user (role=admin)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = (string) $this->argument('email');
        $password = (string) ($this->option('password') ?: 'secret123');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN,
                'partner_id' => 0,
                'partner_code' => 'ADMIN',
                'email_verified_at' => now(),
            ]
        );

        $this->info("Admin upserted: {$user->email}");
        return Command::SUCCESS;
    }
}


