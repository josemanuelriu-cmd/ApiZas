<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Laravel\Passport\Client;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TypeSeeder::class,
            BoardgameSeeder::class,
            ZassessionSeeder::class,
            GameSeeder::class,
            BoardgameTypeSeeder::class,
            UserZassessionSeeder::class,
            GameUserSeeder::class,
        ]);
        
        Client::create([
            'name'          => 'ApiZas Personal Access Client',
            'secret'        => Str::random(40),
            'provider'      => 'users',
            'redirect_uris' => [],
            'grant_types'   => ['personal_access'],
            'revoked'       => false,
        ]);

    }
}
