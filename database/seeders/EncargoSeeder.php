<?php

namespace Database\Seeders;

use App\Enums\Category;
use App\Enums\EstadoEncargo;
use App\Enums\Style;
use App\Models\Encargo;
use App\Models\User;
use Illuminate\Database\Seeder;

class EncargoSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurarse de que haya al menos un usuario existente
        $user = User::first() ?? User::factory()->create();

        // Crear 5 encargos de prueba
        for ($i = 0; $i < 5; $i++) {
            Encargo::create([
                'user_id' => $user->id,
                'description' => 'Pedido de prueba '.($i + 1),
                'height' => rand(20, 100),
                'width' => rand(20, 100),
                'style' => fake()->randomElement(Style::values()),
                'category' => fake()->randomElement(Category::values()),
                'status' => EstadoEncargo::Pending,
                'imagen' => null,
            ]);
        }
    }
}
