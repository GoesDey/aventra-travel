<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Super Admin TriHitaTour',
            'email'    => 'admin@trihitatour.com',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
            'status'   => 'active',
        ]);

        User::create([
            'name'     => 'Ardy',
            'email'    => 'ardy@example.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'status'   => 'active',
        ]);
    }
}
