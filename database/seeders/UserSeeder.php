<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //DB::table('users')->insert([
        User::create([
            'num_partner' => 1,
            'nickname' => 'adminTest',
            'name' => 'adminTest',
            'password' => bcrypt('password1'),
            'type' => 'admin',
            'registration_date' => now(),
            'email' => 'test1@example.com',
            'telephone' => '1234567891',
            'age' => 30,
            'language' => 'es'
        ]);

        User::create([
            'num_partner' => 2,
            'nickname' => 'juntaTest',
            'name' => 'juntaTest',
            'password' => bcrypt('password2'),
            'type' => 'junta',
            'registration_date' => now(),
            'email' => 'test2@example.com',
            'telephone' => '1234567892',
            'age' => 32,
            'language' => 'es'
        ]);

        User::create([
            'num_partner' => 3,
            'nickname' => 'partnerTest',
            'name' => 'partnerTest',
            'password' => bcrypt('password3'),
            'type' => 'partner',
            'registration_date' => now(),
            'email' => 'test3@example.com',
            'telephone' => '1234567893',
            'age' => 66,
            'language' => 'es'
        ]);

        User::create([
            'num_partner' => 4,
            'nickname' => 'guestTest',
            'name' => 'guestTest',
            'password' => bcrypt('password4'),
            'type' => 'guest',
            'registration_date' => now(),
            'email' => 'test4@example.com',
            'telephone' => '1234567894',
            'age' => 66,
            'language' => 'es'
        ]);
    }
}
