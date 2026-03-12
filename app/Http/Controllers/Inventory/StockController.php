<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Alert;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $products = Product::with(['inventory', 'category'])->paginate(10);
        $suppliers = Supplier::all();
        return view('inventory.stock.index', compact('products', 'suppliers'));
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

        $this->stockService->increase(
            $product,
            $request->quantity,
            StockMovement::REASON_MANUAL_ADJUSTMENT,
            'Reorder from supplier — Unit price: ₱' . number_format($request->unit_price, 2)
        );

        return redirect()->route('inventory.stock.index')->with('success', 'Inventory updated successfully');
    }

    public function stockIn(Request $request, Product $product)
    {
        $request->validate([
            'reason' => ['required', 'in:purchase_order,customer_return,manual_adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->stockService->increase(
            $product,
            $request->quantity,
            $request->reason,
            $request->notes
        );

        return redirect()->route('inventory.stock.index')
            ->with('success', "Stock in of {$request->quantity} units recorded for {$product->name}");
    }

    public function stockOut(Request $request, Product $product)
    {
        $currentStock = $product->inventory->quantity ?? 0;

        $request->validate([
            'reason' => ['required', 'in:damaged,lost_stolen,manual_adjustment,expired'],
            'quantity' => ['required', 'integer', 'min:1', "max:{$currentStock}"],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->stockService->decrease(
            $product,
            $request->quantity,
            $request->reason,
            $request->notes
        );

        return redirect()->route('inventory.stock.index')
            ->with('success', "Stock out of {$request->quantity} units recorded for {$product->name}");
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'user'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(15)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('inventory.stock.movements', compact('movements', 'products'));
    }
}
