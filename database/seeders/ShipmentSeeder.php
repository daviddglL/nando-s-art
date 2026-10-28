<?php

namespace Database\Seeders;

use App\Enums\Category;
use App\Enums\ShipmentStatus;
use App\Enums\Style;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurarse de que haya al menos un usuario existente
        $user = User::first() ?? User::factory()->create();

        // Crear 5 pedidos de prueba
        for ($i = 0; $i < 5; $i++) {
            Shipment::create([
                'user_id' => $user->id,
                'description' => 'Pedido de prueba '.($i + 1),
                'height' => rand(20, 100),
                'width' => rand(20, 100),
                'style' => fake()->randomElement(Style::values()),
                'category' => fake()->randomElement(Category::values()),
                'status' => ShipmentStatus::Pending,
                'imagen' => null,
            ]);
        }
    }
}
