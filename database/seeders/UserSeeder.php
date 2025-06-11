<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin'),
                'role' => 'admin',
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'nando',
                'email' => 'info@fernandodelgadoart.com',
                'password' => bcrypt('75139211'),
                'role' => 'admin',
                'remember_token' => Str::random(10),
            ],

        ]);
    }
}
