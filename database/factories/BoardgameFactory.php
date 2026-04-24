<?php

namespace Database\Factories;

use App\Models\Boardgames;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Boardgames>
 */
class BoardgamesFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'min_players' => fake()->numberBetween(3, 4),
            'max_players' => fake()->numberBetween(4, 10),
            'min_age' => fake()->numberBetween(8, 18),
            'duration' => fake()->numberBetween(30, 180),
            'description' => fake()->text(),
            'owner_user_id' => null
        ];
    }

    
}
