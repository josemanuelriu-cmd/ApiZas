<?php

namespace Database\Factories;

use App\Models\Zassession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Zassession>
 */
class ZassessionFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startHour = $this->faker->numberBetween(8, 18);
        $endHour = $this->faker->numberBetween($startHour + 1, 23);
        return [            
            'name' => $this->faker->unique()->name(),
            'event_name' => $this->faker->name(),
            'date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),            
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time'   => sprintf('%02d:00:00', $endHour),
            'max_users' => $this->faker->numberBetween(1, 100),
            'direction' => $this->faker->address(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }
}
