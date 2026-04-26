<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Boardgame;

class BoardgameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Boardgame::create([
            'name' => 'Bang!', 
            'slug' => 'bang1', 
            'min_players' => 3, 
            'max_players' => 7, 
            'min_age' => 10, 
            'duration' => 100, 
            'description' => 'Lejano oeste',
            'owner_user_id' => null
        ]);

        Boardgame::create([
            'name' => 'Carcassonne', 
            'slug' => 'carcassonne', 
            'min_players' => 2, 
            'max_players' => 5, 
            'min_age' => 10, 
            'duration' => 60, 
            'description' => 'ciudad de carcassonne',
            'owner_user_id' => null
        ]);
    }
}
