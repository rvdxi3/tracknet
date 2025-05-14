<?php

// app/Http/Controllers/Sales/SalesController.php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Customer;

class SalesController extends Controller
{
    public function dashboard()
    {
        $totalCustomers = Customer::count();
        $totalOrders = Order::count();
        $pendingOrders = Sale::where('payment_status', 'pending')->count();
        $completedOrders = Sale::where('payment_status', 'paid')->count();
        
        $recentOrders = Order::with(['user', 'sale'])->latest()->take(5)->get();
        $recentCustomers = Customer::latest()->take(5)->get();
        
        return view('sales.dashboard', compact(
            'totalCustomers',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'recentOrders',
            'recentCustomers'
        ));
    }
}