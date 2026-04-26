<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Zassession;

class UserZassessionSeeder extends Seeder
{
    public function run(): void
    {
        $zassession1 = Zassession::first();
        $zassession2 = Zassession::skip(1)->first();

        $admin   = User::first();
        $junta   = User::skip(1)->first();
        $partner = User::skip(2)->first();

        $zassession1->users()->attach([$admin->id, $junta->id]);
        $zassession2->users()->attach([$partner->id]);
    }
}
