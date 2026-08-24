<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@casayacobone.com',
            'password' => Hash::make('Yacobone2026'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Vendedor',
            'email' => 'vendedor@casayacobone.com',
            'password' => Hash::make('password'),
            'role' => 'vendedor',
            'email_verified_at' => now(),
        ]);
    }
}
