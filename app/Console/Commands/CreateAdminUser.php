<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Create Admin User';

    public function handle()
    {
        $email = 'admin@company.com';

        if (User::where('email', $email)->exists()) {
            $this->error('Admin already exists!');
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'approval_status' => 'approved',
            'is_active' => 1,
            'approved_at' => now(),
            'approved_by' => 1,
        ]);

        $this->info('Admin created successfully!');
    }
}