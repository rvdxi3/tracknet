<?php

// database/seeders/PurchaseOrdersTableSeeder.php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PurchaseOrdersTableSeeder extends Seeder
{
    public function run()
    {
        $suppliers = Supplier::all();
        $inventoryUser = User::where('email', 'inventory@example.com')->first();
        $products = Product::all();
        
        for ($i = 0; $i < 5; $i++) {
            $po = PurchaseOrder::create([
                'supplier_id' => $suppliers->random()->id,
                'user_id' => $inventoryUser->id,
                'po_number' => 'PO-' . Str::upper(Str::random(8)),
                'order_date' => now()->subDays(rand(1, 30)),
                'expected_delivery_date' => now()->addDays(rand(1, 14)),
                'status' => rand(0, 1) ? 'delivered' : 'pending',
                'notes' => rand(0, 1) ? 'Urgent order - please prioritize' : null
            ]);
            
            // Add 2-5 items to each PO
            $itemsCount = rand(2, 5);
            $selectedProducts = $products->random($itemsCount);
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(5, 20);
                $unitPrice = $product->price * 0.8; // 20% discount for bulk
                
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $quantity * $unitPrice
                ]);
                
                // If PO is delivered, update inventory
                if ($po->status == 'delivered') {
                    $inventory = $product->inventory;
                    $inventory->increment('quantity', $quantity);
                }
            }
        }
    }
}
