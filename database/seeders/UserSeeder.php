<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Function Run
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@tomogame.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'remember_token' => Str::random(10),
            'role' => 'ADMINISTRATOR',
            'plain_token' => '',
        ]);
        DB::table('users')->insert([
            'name' => 'Terminal 1',
            'email' => 'terminal1@tomogame.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'remember_token' => Str::random(10),
            'role' => 'TERMINAL',
            'plain_token' => '',
        ]);
    }
}
