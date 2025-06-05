<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Str;

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
                'description' => 'Pedido de prueba ' . ($i + 1),
                'height' => rand(20, 100),
                'width' => rand(20, 100),
                'style' => fake()->randomElement(['abstracto', 'realismo', 'impresionismo']),
                'category' => fake()->randomElement(['paisaje', 'urbano', 'retrato']),
                'status' => 'pending',
                'imagen' => null, // Opcional: puedes agregar rutas si quieres
            ]);
        }
    }
}
