<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Ejecuta los seeders necesarios
        $this->call([
            LocationSeeder::class,
            UserLevelSeeder::class,
        ]);
    }
}
