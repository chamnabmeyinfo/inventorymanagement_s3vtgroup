<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminUser extends Command
{
    protected $signature = 'inventory:reset-admin
                            {--email=admininvs3@s3vtgroup.com.kh : New admin email}
                            {--password=s3admin@123$$ : New admin password}';

    protected $description = 'Reset admin user credentials (updates existing admin or creates one)';

    public function handle(): int
    {
        $email = $this->option('email');
        $password = $this->option('password');

        $admin = User::where('role', 'admin')->first();

        if ($admin) {
            $admin->update([
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $this->info("Admin updated. Login with: {$email}");
        } else {
            User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
            ]);
            $this->info("Admin created. Login with: {$email}");
        }

        return self::SUCCESS;
    }
}
