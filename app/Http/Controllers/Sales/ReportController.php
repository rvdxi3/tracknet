<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Date range filter (default: last 12 months)
        $period = $request->get('period', '12months');

        [$startDate, $endDate] = $this->resolveDateRange($period, $request);

        // ── Key Metrics ──────────────────────────────────────────────
        $totalRevenue = Sale::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        $paidOrders = Sale::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $cancelledOrders = Sale::where('fulfillment_status', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $avgOrderValue = $paidOrders > 0
            ? round($totalRevenue / $paidOrders, 2)
            : 0;

        $totalCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // ── Monthly Revenue (last 12 months) ─────────────────────────
        $monthlyRevenue = Sale::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyLabels  = [];
        $monthlyData    = [];
        $monthlyOrders  = [];
        foreach ($monthlyRevenue as $row) {
            $monthlyLabels[] = date('M Y', mktime(0, 0, 0, $row->month, 1, $row->year));
            $monthlyData[]   = (float) $row->revenue;
            $monthlyOrders[] = (int)   $row->orders;
        }

        // ── Daily Orders — last 30 days ───────────────────────────────
        $dailyOrders = Order::where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dailyLabels  = [];
        $dailyCounts  = [];
        $dailyRevenue = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyLabels[]  = now()->subDays($i)->format('M d');
            $dailyCounts[]  = $dailyOrders->has($date) ? (int) $dailyOrders[$date]->count   : 0;
            $dailyRevenue[] = $dailyOrders->has($date) ? (float) $dailyOrders[$date]->revenue : 0;
        }

        // ── Order Status Breakdown ────────────────────────────────────
        $statusBreakdown = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select('fulfillment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('fulfillment_status')
            ->pluck('count', 'fulfillment_status')
            ->toArray();

        // ── Payment Status Breakdown ──────────────────────────────────
        $paymentBreakdown = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status')
            ->toArray();

        // ── Top Selling Products ──────────────────────────────────────
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // ── Revenue by Category ───────────────────────────────────────
        $categoryRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.total_price) as revenue'),
                DB::raw('SUM(order_items.quantity) as qty')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        // ── Payment Method Distribution ───────────────────────────────
        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method')
            ->toArray();

        // ── New Customers per Month (last 6 months) ───────────────────
        $newCustomers = User::where('role', 'customer')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $customerLabels = [];
        $customerCounts = [];
        foreach ($newCustomers as $row) {
            $customerLabels[] = date('M Y', mktime(0, 0, 0, $row->month, 1, $row->year));
            $customerCounts[] = (int) $row->count;
        }

        // ── Recent Transactions ───────────────────────────────────────
        $recentTransactions = Order::with(['user', 'sale', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.reports.index', compact(
            'period', 'startDate', 'endDate',
            'totalRevenue', 'totalOrders', 'paidOrders', 'cancelledOrders',
            'avgOrderValue', 'totalCustomers',
            'monthlyLabels', 'monthlyData', 'monthlyOrders',
            'dailyLabels', 'dailyCounts', 'dailyRevenue',
            'statusBreakdown', 'paymentBreakdown',
            'topProducts', 'categoryRevenue',
            'paymentMethods',
            'customerLabels', 'customerCounts',
            'recentTransactions'
        ));
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', '12months');
        [$startDate, $endDate] = $this->resolveDateRange($period, $request);

        $totalRevenue = Sale::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $paidOrders = Sale::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $cancelledOrders = Sale::where('fulfillment_status', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $avgOrderValue = $paidOrders > 0 ? round($totalRevenue / $paidOrders, 2) : 0;
        $totalCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate])->count();

        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.total_price) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')->limit(10)->get();

        $categoryRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('categories.name', DB::raw('SUM(order_items.total_price) as revenue'), DB::raw('SUM(order_items.quantity) as qty'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')->get();

        $recentTransactions = Order::with(['user', 'sale'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()->take(20)->get();

        $pdf = Pdf::loadView('sales.reports.pdf', compact(
            'startDate', 'endDate',
            'totalRevenue', 'totalOrders', 'paidOrders', 'cancelledOrders',
            'avgOrderValue', 'totalCustomers',
            'topProducts', 'categoryRevenue', 'recentTransactions'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('sales-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
    }

    public function export(Request $request)
    {
        $period = $request->get('period', '12months');
        [$startDate, $endDate] = $this->resolveDateRange($period, $request);

        $orders = Order::with(['user', 'sale', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        $filename = 'sales-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Customer', 'Email', 'Date', 'Items', 'Subtotal', 'Tax', 'Shipping', 'Total', 'Payment Method', 'Payment Status', 'Fulfillment Status']);
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    $order->created_at->format('Y-m-d'),
                    $order->items->count(),
                    number_format($order->subtotal, 2),
                    number_format($order->tax, 2),
                    number_format($order->shipping, 2),
                    number_format($order->total, 2),
                    ucfirst(str_replace('_', ' ', $order->payment_method)),
                    $order->sale ? ucfirst($order->sale->payment_status) : 'N/A',
                    $order->sale ? ucfirst($order->sale->fulfillment_status) : 'N/A',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function resolveDateRange(string $period, Request $request): array
    {
        return match ($period) {
            '7days'    => [now()->subDays(6)->startOfDay(),    now()->endOfDay()],
            '30days'   => [now()->subDays(29)->startOfDay(),   now()->endOfDay()],
            '3months'  => [now()->subMonths(3)->startOfMonth(),now()->endOfDay()],
            '6months'  => [now()->subMonths(6)->startOfMonth(),now()->endOfDay()],
            'this_month' => [now()->startOfMonth(),            now()->endOfDay()],
            'last_month' => [now()->subMonth()->startOfMonth(),now()->subMonth()->endOfMonth()],
            'this_year'  => [now()->startOfYear(),             now()->endOfDay()],
            'custom'   => [
                $request->filled('from') ? \Carbon\Carbon::parse($request->from)->startOfDay() : now()->subMonths(12)->startOfMonth(),
                $request->filled('to')   ? \Carbon\Carbon::parse($request->to)->endOfDay()     : now()->endOfDay(),
            ],
            default    => [now()->subMonths(11)->startOfMonth(), now()->endOfDay()], // 12months
        };
    }
}
