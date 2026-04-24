<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Type;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Type::create([
            'type' => 'abstracto',
            'description' => 'Test abstracto Description',
        ]);

        Type::create([
            'type' => 'ameritrash',
            'description' => 'Test ameritrash Description',
        ]);

        Type::create([
            'type' => 'dados',
            'description' => 'Test dados Description',
        ]);
    }
}
