<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Game;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Game::create([
            'zassession_id' => 1,
            'boardgame_id' => 1,
            'host_user_id' => 1,
            'max_players' => 4,
            'start_time' => '17:00:00',
            'status' => 'open',
            'necesary_know_how' => true,            
        ]);

        Game::create([
            'zassession_id' => 1,
            'boardgame_id' => 2,
            'host_user_id' => 2,
            'max_players' => 5,
            'start_time' => '18:00:00',
            'status' => 'limited',
            'necesary_know_how' => false,
        ]);
    }
}
