<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Zassession;
use App\Models\Boardgame;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class GameFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $zassession = Zassession::factory()->create();
        $boardgame  = Boardgame::factory()->create();
        $user       = User::factory()->create();
        return [            
            'zassession_id' => $zassession->id,
            'boardgame_id' => $boardgame->id,
            'host_user_id' => $user->id,
            'max_players' => fake()->numberBetween(2, 12),
            'start_time' => sprintf('%02d:00:00', fake()->numberBetween(9, 21)),
            'status' => fake()->randomElement(['open', 'limited', 'playing', 'finished']),
            'necesary_know_how' => fake()->boolean(),
        ];
    }
}
