<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Idempotent: creates the admin once and never overwrites it, so
     * redeploys cannot reset a password you changed.
     * The password comes from SEED_ADMIN_PASSWORD (never hard-coded).
     */
    public function run(): void
    {
        $email    = config('sumbungan.admin_email');
        $password = config('sumbungan.admin_password');

        if (User::where('email', $email)->exists()) {
            return;
        }

        if (! $password || strlen($password) < 8) {
            $this->command?->warn('Admin NOT created: set SEED_ADMIN_PASSWORD (min 8 chars) and redeploy.');
            return;
        }

        User::create([
            'name'     => config('sumbungan.admin_name'),
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'admin',
        ]);
        $this->command?->info("Admin created: {$email}");
    }
}
