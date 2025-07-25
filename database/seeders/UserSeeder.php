<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'ite@gmail.com')->exists()) {
            $admin = User::create([
                'name' => 'admin',
                'email' => 'ite@gmail.com',
                'password' => Hash::make('*Lobacude13*'), // Cambia '12345678' por tu contraseña preferida
                'email_verified_at' => now(),
            ]);
        }
    }
}
