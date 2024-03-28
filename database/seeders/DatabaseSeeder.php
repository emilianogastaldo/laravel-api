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
        // Creo un utente
        \App\Models\User::factory()->create([
            'name' => 'Emiliano',
            'email' => 'emiliano@test.com',
        ]);

        // Riempio la tabella Types
        $this->call([TypeSeeder::class, TechnologySeeder::class]);

        // Creo i finti progetti
        \App\Models\Project::factory(10)->create();
    }
}
