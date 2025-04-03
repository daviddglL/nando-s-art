<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $shipment = new Shipment();
        $shipment->user_id = User::find(1)->id;
        $shipment->product_id = Product::find(1)->id;
        $shipment->tracking_number = '1';
        $shipment->quantity = 1;
        $shipment->status = 'pending';
        $shipment->save();
    }
}
