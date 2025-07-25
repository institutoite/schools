<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserSeeder::class,
            // Add other seeders here as needed
            // For example:
            // ColegioSeeder::class,
            // UbicacionSeeder::class,
            // AmbienteSeeder::class,
            // ServicioSeeder::class,
        ]);
        // You can add more seeders as needed
    }
}
