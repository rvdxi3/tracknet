<?php

// app/Http/Controllers/Website/CartController.php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Auth::user()->cart;
        $cartItems = $cart ? $cart->items()->with('product.inventory')->get() : collect();
        
        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        
        $taxRate = config('cart.tax_rate', 0);
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;
        
        return view('website.cart.index', compact('cartItems', 'subtotal', 'tax', 'total'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);
        
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;
        
        // Check if product is in stock
        if ($product->stock < $quantity) {
            return back()->with('error', 'Not enough stock available');
        }
        
        $user = Auth::user();
        
        // Get or create cart for user
        $cart = $user->cart ?? Cart::create(['user_id' => $user->id]);
        
        // Check if product already in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        
        if ($cartItem) {
            // Update quantity
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity
            ]);
        } else {
            // Add new item to cart
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity
            ]);
        }
        
        return back()->with('success', 'Product added to cart');
    }
    
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cartItem->product->stock
        ]);
        
        $cartItem->update([
            'quantity' => $request->quantity
        ]);
        
        return redirect()->route('cart.index')->with('success', 'Cart updated');
    }
    
    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        
        return redirect()->route('cart.index')->with('success', 'Item removed from cart');
    }
}