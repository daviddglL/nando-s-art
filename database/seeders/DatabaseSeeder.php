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
        $this->call(UserSeeder::class);

        // Las obras y los presupuestos de ejemplo no se crean en producción: allí las sube el artista desde /admin/obras
        if (! app()->environment('production')) {
            $this->call([
                ProductSeeder::class,
                EncargoSeeder::class,
            ]);
        }
    }
}
