<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlamatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Running
        DB::table('alamats')->insert([
            'user_id' => 4,
            'alamat' => 'JL Pemuda 73',
            'province_id' => 10,
            'kota_id' => 250,
        ]);
    }
}
