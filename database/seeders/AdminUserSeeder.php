<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the first super_admin account. Prints a generated password
     * once to the console -- this is a temporary bootstrap login while
     * Google OAuth is deferred (see project notes), swap it out later.
     */
    public function run(): void
    {
        $email = env('ADMIN_SEED_EMAIL', 'it@mahardika.ac.id');
        $password = env('ADMIN_SEED_PASSWORD') ?: Str::password(14);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin JDIH UIM',
                'password' => $password,
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $this->command->info("Admin user ready: {$user->email}");
        if (! env('ADMIN_SEED_PASSWORD')) {
            $this->command->warn("Generated password (save this now): {$password}");
        }
    }
}
