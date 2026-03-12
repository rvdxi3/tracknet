<?php

// app/Http/Controllers/Sales/SalesController.php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function dashboard()
    {
        // ── Basic counts ──────────────────────────────────────────────
        $totalCustomers  = User::where('role', 'customer')->count();
        $totalOrders     = Order::count();
        $ordersToday     = Order::whereDate('created_at', today())->count();
        $pendingOrders   = Sale::where('fulfillment_status', 'pending')->count();
        $completedOrders = Sale::where('payment_status', 'paid')->count();

        $totalRevenue = Sale::where('payment_status', 'paid')->sum('total_amount');

        $todaySales = Sale::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $monthlySales = Sale::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // ── Order status breakdown (for doughnut chart) ───────────────
        $statusBreakdown = Sale::select('fulfillment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('fulfillment_status')
            ->pluck('count', 'fulfillment_status')
            ->toArray();

        // ── Revenue last 7 days (for line chart) ──────────────────────
        $revenueWeekRaw = Order::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $weekLabels  = [];
        $weekRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $weekLabels[]  = now()->subDays($i)->format('D M d');
            $weekRevenue[] = $revenueWeekRaw->has($date) ? (float) $revenueWeekRaw[$date]->revenue : 0;
        }

        // ── Top 5 products ────────────────────────────────────────────
        $topProducts = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.total_price) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ── Recent orders ─────────────────────────────────────────────
        $recentOrders = Order::with(['user', 'sale'])->latest()->take(5)->get();

        return view('sales.dashboard', compact(
            'totalCustomers', 'totalOrders', 'ordersToday',
            'pendingOrders', 'completedOrders', 'totalRevenue',
            'todaySales', 'monthlySales',
            'statusBreakdown', 'weekLabels', 'weekRevenue',
            'topProducts', 'recentOrders'
        ));
    }
}
