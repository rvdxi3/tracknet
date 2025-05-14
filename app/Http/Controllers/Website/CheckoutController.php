<?php

// app/Http/Controllers/Website/CheckoutController.php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Auth::user()->cart;
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        $cartItems = $cart->items()->with('product.inventory')->get();
        
        // Verify all items are in stock
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', "{$item->product->name} doesn't have enough stock");
            }
        }
        
        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        
        $taxRate = config('cart.tax_rate', 0);
        $tax = $subtotal * ($taxRate / 100);
        $shipping = 0; // Free shipping for now
        $total = $subtotal + $tax + $shipping;
        
        return view('website.checkout.index', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'payment_method' => 'required|in:cod,credit_card,paypal',
            'notes' => 'nullable|string'
        ]);
        
        $user = Auth::user();
        $cart = $user->cart;
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        $cartItems = $cart->items()->with('product.inventory')->get();
        
        // Verify all items are in stock
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', "{$item->product->name} doesn't have enough stock");
            }
        }
        
        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        
        $taxRate = config('cart.tax_rate', 0);
        $tax = $subtotal * ($taxRate / 100);
        $shipping = 0; // Free shipping for now
        $total = $subtotal + $tax + $shipping;
        
        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'shipping_address' => $request->shipping_address,
            'billing_address' => $request->same_billing ? $request->shipping_address : $request->billing_address,
            'notes' => $request->notes
        ]);
        
        // Add order items
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->product->price,
                'total_price' => $cartItem->product->price * $cartItem->quantity
            ]);
            
            // Update inventory
            $inventory = Inventory::where('product_id', $cartItem->product_id)->first();
            if ($inventory) {
                $inventory->decrement('quantity', $cartItem->quantity);
                
                // Check for low stock
                if ($inventory->quantity <= $inventory->low_stock_threshold) {
                    // Create alert (you can also implement notification system)
                    Alert::create([
                        'product_id' => $cartItem->product_id,
                        'type' => 'low_stock',
                        'message' => "Product {$cartItem->product->name} is low on stock ({$inventory->quantity} remaining)"
                    ]);
                }
            }
        }
        
        // Clear the cart
        $cart->items()->delete();
        
        // Create sale record (to be processed by sales department)
        Sale::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'total_amount' => $total,
            'payment_status' => 'pending',
            'fulfillment_status' => 'pending'
        ]);
        
        return redirect()->route('account.orders.show', $order)->with('success', 'Order placed successfully!');
    }
}