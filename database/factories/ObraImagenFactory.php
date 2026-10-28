<?php

namespace Database\Factories;

use App\Models\ObraImagen;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ObraImagen>
 */
class ObraImagenFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'imagen' => fake()->uuid().'.webp',
        ];
    }
}
