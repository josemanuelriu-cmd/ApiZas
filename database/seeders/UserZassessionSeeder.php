<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Zassession;

class UserZassessionSeeder extends Seeder
{
    public function run(): void
    {
        $zassession1 = Zassession::where('name', 'Zassession 1')->first();
        $zassession2 = Zassession::where('name', 'Zassession 2')->first();

        $admin   = User::where('email', 'test1@example.com')->first();
        $junta   = User::where('email', 'test2@example.com')->first();
        $partner = User::where('email', 'test3@example.com')->first();

        $zassession1->users()->attach([$admin->id, $junta->id]);
        $zassession2->users()->attach([$partner->id]);
    }
}
