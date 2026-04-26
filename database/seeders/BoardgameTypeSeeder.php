<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Boardgame;
use App\Models\Type;

class BoardgameTypeSeeder extends Seeder
{
    public function run(): void
    {
        $bang        = Boardgame::first();
        $carcassonne = Boardgame::skip(1)->first();

        $abstracto   = Type::first();
        $ameritrash  = Type::skip(1)->first();
        $dados       = Type::skip(2)->first();

        $bang->types()->attach([$abstracto->id, $ameritrash->id]);
        $carcassonne->types()->attach([$abstracto->id, $dados->id]);
    }
}
