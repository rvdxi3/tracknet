<?php

// database/seeders/InventoryTableSeeder.php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class InventoryTableSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => rand(5, 50),
                'low_stock_threshold' => 5
            ]);
        }
    }
}
