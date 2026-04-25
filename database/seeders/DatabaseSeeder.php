<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

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
        ]);
        Artisan::call('passport:client', [
            '--personal' => true,
            '--name'     => 'ApiZas Personal Access Client',
        ]);
    }
}
