<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Zassession;

class ZassessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Zassession::create([
            'name' => 'Zassession 1',
            'event_name' => 'Cartas Event',
            'date' => '2026-10-10',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'max_users' => 10,
            'direction' => 'Cartas Direction',
            'latitude' => 41.434088,
            'longitude' => 2.179224,
            'created_at' => now(),
        ]);

        Zassession::create([
            'name' => 'Zassession 2',
            'event_name' => 'Ameritrash Event',
            'date' => '2026-10-11',
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'max_users' => 15,
            'direction' => 'Ameritrash Direction',
            'latitude' => 41.434088,
            'longitude' => 2.179224,
            'created_at' => now(),
        ]);
    }
}

