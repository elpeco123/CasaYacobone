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
        $adminEmail = env('SEEDER_ADMIN_EMAIL', 'admin@casayacobone.com');
        $adminPassword = env('SEEDER_ADMIN_PASSWORD', 'admin123');

        $vendedorEmail = env('SEEDER_VENDEDOR_EMAIL', 'vendedor@casayacobone.com');
        $vendedorPassword = env('SEEDER_VENDEDOR_PASSWORD', 'vendedor123');

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Administrador',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => $vendedorEmail],
            [
                'name' => 'Vendedor',
                'password' => Hash::make($vendedorPassword),
                'role' => 'vendedor',
                'email_verified_at' => now(),
            ]
        );
    }
}
