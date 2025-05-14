<?php

// app/Http/Controllers/Inventory/StockController.php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Alert;
use App\Models\Supplier;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::with('inventory')->paginate(10);
        return view('inventory.stock.index', compact('products'));
    }
    
    public function alerts()
    {
        $alerts = Alert::with('product')->latest()->paginate(10);
        return view('inventory.stock.alerts', compact('alerts'));
    }
    
    public function markAsRead(Alert $alert)
    {
        $alert->update(['is_read' => true]);
        return back()->with('success', 'Alert marked as read');
    }
    
    public function reorder(Product $product)
    {
        $suppliers = Supplier::all();
        return view('inventory.stock.reorder', compact('product', 'suppliers'));
    }
    
    public function processReorder(Request $request, Product $product)
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0']
        ]);
        
        // In a real application, you would create a purchase order here
        // For simplicity, we'll just update the inventory directly
        
        $inventory = $product->inventory;
        $inventory->increment('quantity', $request->quantity);
        
        // Mark any low stock alerts as read
        Alert::where('product_id', $product->id)
            ->where('type', 'low_stock')
            ->update(['is_read' => true]);
            
        return redirect()->route('inventory.stock.index')->with('success', 'Inventory updated successfully');
    }
}