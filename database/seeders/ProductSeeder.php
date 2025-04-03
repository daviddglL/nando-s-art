<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('products')->insert([
            'name' => 'Product 1',
            'description' => 'Product 1 description',
            'measures' => '10x10',
            'style' => 'acuarela',
            'imagen' => 'product1.jpg',
            'category' => 'retrato',
        ]);
            
    }
}
