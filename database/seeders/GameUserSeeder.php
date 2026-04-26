<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\User;

class GameUserSeeder extends Seeder
{
    public function run(): void
    {
        $game1 = Game::where('start_time', '17:00:00')->first();
        $game2 = Game::where('start_time', '18:00:00')->first();

        $admin   = User::where('email', 'test1@example.com')->first();
        $junta   = User::where('email', 'test2@example.com')->first();
        $partner = User::where('email', 'test3@example.com')->first();

        $game1->players()->attach([$admin->id, $junta->id]);
        $game2->players()->attach([$partner->id]);
    }
}
