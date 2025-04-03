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
            'name' => 'admin',
            'email' => 'admin@exmaple.com',
            'password' => bcrypt('admin'),
            'address' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'IL',
            'country' => 'USA',
            'zip' => '62701',
            'phone' => '217-555-1212',  
            'role' => 'admin',
            'remember_token' => Str ::random(10),
        ]);
    }
}
