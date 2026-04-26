<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\User;

class GameUserSeeder extends Seeder
{
    public function run(): void
    {
        $game1 = Game::first();
        $game2 = Game::skip(1)->first();

        $admin   = User::first();
        $junta   = User::skip(1)->first();
        $partner = User::skip(2)->first();

        $game1->players()->attach([$admin->id, $junta->id]);
        $game2->players()->attach([$partner->id]);
    }
}
