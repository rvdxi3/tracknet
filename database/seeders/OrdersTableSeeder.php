<?php

// database/seeders/OrdersTableSeeder.php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();
        $salesUser = User::findByEmail('sales@example.com');
        
        for ($i = 0; $i < 10; $i++) {
            $customer = $customers->random();
            $cartItems = $products->random(rand(1, 5));
            
            $subtotal = $cartItems->sum(function ($product) {
                $quantity = rand(1, 3);
                return $product->price * $quantity;
            });
            
            $taxRate = 10; // 10% tax
            $tax = $subtotal * ($taxRate / 100);
            $shipping = 0; // Free shipping
            $total = $subtotal + $tax + $shipping;
            
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => ['cod', 'credit_card', 'paypal'][rand(0, 2)],
                'shipping_address' => "{$customer->name}\n123 Main St\nAnytown, CA 12345",
                'billing_address' => null,
                'notes' => rand(0, 1) ? 'Please deliver after 5pm' : null
            ]);
            
            // Add order items
            foreach ($cartItems as $product) {
                $quantity = rand(1, 3);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $quantity
                ]);
                
                // Update inventory
                $inventory = $product->inventory;
                $inventory->decrement('quantity', $quantity);
                
                // Check for low stock
                if ($inventory->quantity <= $inventory->low_stock_threshold) {
                    Alert::create([
                        'product_id' => $product->id,
                        'type' => 'low_stock',
                        'message' => "Product {$product->name} is low on stock ({$inventory->quantity} remaining)"
                    ]);
                }
            }
            
            // Create sale record
            $paymentStatus = ['pending', 'paid', 'failed'][rand(0, 2)];
            $fulfillmentStatus = $paymentStatus == 'paid' ? ['processing', 'shipped', 'delivered'][rand(0, 2)] : 'pending';
            
            Sale::create([
                'user_id' => $salesUser->id,
                'order_id' => $order->id,
                'total_amount' => $total,
                'payment_status' => $paymentStatus,
                'fulfillment_status' => $fulfillmentStatus
            ]);
        }
    }
}
