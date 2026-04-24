<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Type>
 */
class TypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [            
            'type' => $this->faker->unique()->randomElement(['abstracto', 'ameritrash', 'cartas', 'clásico', 'colocación de trabajadores', 'construcción de mazos', 'cooperativo', 'dados', 'escape room', 'estrategia', 'eurogame', 'familiar', 'filler', 'infantil', 'investigacion', 'mayorias', 'narrativo', 'party', 'roles ocultos', 'wargame']),
            'description' => fake()->text(),
        ];
    }
}
    
