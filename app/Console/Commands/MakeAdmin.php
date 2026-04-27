<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin
                            {--name= : Admin user name}
                            {--email= : Admin user email}
                            {--password= : Admin user password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or promote an active approved admin user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Admin name');
        $email = $this->option('email') ?: $this->ask('Admin email');
        $password = $this->option('password') ?: $this->secret('Admin password');

        if (! $name || ! $email || ! $password) {
            $this->error('Name, email, and password are required.');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user) {
            $user->update([
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin',
                'approval_status' => 'approved',
                'is_active' => true,
                'approved_at' => now(),
                'approved_by' => $user->id,
            ]);

            $this->info('Existing user promoted/updated as admin successfully.');
            $this->line("Email: {$user->email}");

            return self::SUCCESS;
        }

        $created = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'approval_status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => null,
        ]);

        $created->update(['approved_by' => $created->id]);

        $this->info('Admin user created successfully.');
        $this->line("Email: {$created->email}");

        return self::SUCCESS;
    }
}
