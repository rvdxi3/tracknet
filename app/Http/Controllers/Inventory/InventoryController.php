<?php

// app/Http/Controllers/Inventory/InventoryController.php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Alert;

class InventoryController extends Controller
{
    public function dashboard()
    {
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereHas('inventory', function($query) {
            $query->whereRaw('quantity <= low_stock_threshold');
        })->count();
        
        $pendingPurchaseOrders = PurchaseOrder::where('status', 'pending')->count();
        $alerts = Alert::where('is_read', false)->count();
        
        $recentAlerts = Alert::with('product')->latest()->take(5)->get();
        $recentPurchaseOrders = PurchaseOrder::latest()->take(5)->get();
        
        return view('inventory.dashboard', compact(
            'totalProducts',
            'lowStockProducts',
            'pendingPurchaseOrders',
            'alerts',
            'recentAlerts',
            'recentPurchaseOrders'
        ));
    }
}
