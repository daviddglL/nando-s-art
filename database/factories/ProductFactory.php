<?php

namespace Database\Factories;

use App\Enums\Category;
use App\Enums\Style;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'height' => fake()->numberBetween(10, 100),
            'width' => fake()->numberBetween(10, 100),
            'style' => fake()->randomElement(Style::cases()),
            'category' => fake()->randomElement(Category::cases()),
            'imagen' => fake()->uuid().'.webp',
        ];
    }
}
