<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Boardgame;
use App\Models\Type;

class BoardgameTypeSeeder extends Seeder
{
    public function run(): void
    {
        $bang        = Boardgame::where('slug', 'bang1')->first();
        $carcassonne = Boardgame::where('slug', 'carcassonne')->first();

        $abstracto   = Type::where('type', 'abstracto')->first();
        $ameritrash  = Type::where('type', 'ameritrash')->first();
        $dados       = Type::where('type', 'dados')->first();

        $bang->types()->attach([$abstracto->id, $ameritrash->id]);
        $carcassonne->types()->attach([$abstracto->id, $dados->id]);
    }
}
