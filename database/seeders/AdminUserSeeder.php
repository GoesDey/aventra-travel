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
            'name'     => 'Admin Aventra',
            'email'    => 'admin@aventra.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Ardy',
            'email'    => 'ardy@example.com',
            'password' => Hash::make('password'),
            'role'     => 'customer',
        ]);
    }
}
