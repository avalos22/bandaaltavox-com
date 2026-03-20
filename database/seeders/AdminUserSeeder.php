<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@bandaaltavox.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'phone' => '',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('Super Admin');
    }
}
