<?php

namespace Database\Factories;

use App\Enums\Category;
use App\Enums\EstadoEncargo;
use App\Enums\Style;
use App\Models\Encargo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Encargo>
 */
class EncargoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => fake()->sentence(),
            'height' => fake()->numberBetween(10, 100),
            'width' => fake()->numberBetween(10, 100),
            'style' => fake()->randomElement(Style::values()),
            'category' => fake()->randomElement(Category::values()),
            'imagen' => null,
            'status' => EstadoEncargo::Pending,
        ];
    }
}
