<?php

// app/Http/Controllers/Inventory/PurchaseOrderController.php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'user'])->latest()->paginate(10);
        return view('inventory.purchase-orders.index', compact('purchaseOrders'));
    }
    
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::with('inventory')->get();
        return view('inventory.purchase-orders.create', compact('suppliers', 'products'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['required', 'date', 'after_or_equal:order_date'],
            'notes' => ['nullable', 'string'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.unit_price' => ['required', 'numeric', 'min:0']
        ]);
        
        // Create purchase order
        $purchaseOrder = PurchaseOrder::create([
            'supplier_id' => $request->supplier_id,
            'user_id' => auth()->id(),
            'po_number' => 'PO-' . Str::upper(Str::random(8)),
            'order_date' => $request->order_date,
            'expected_delivery_date' => $request->expected_delivery_date,
            'status' => 'pending',
            'notes' => $request->notes
        ]);
        
        // Add items to purchase order
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            
            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['unit_price'],
                'total_price' => $productData['quantity'] * $productData['unit_price']
            ]);
        }
        
        return redirect()->route('inventory.purchase-orders.show', $purchaseOrder)->with('success', 'Purchase order created successfully');
    }
    
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'items.product']);
        return view('inventory.purchase-orders.show', compact('purchaseOrder'));
    }
    
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status != 'pending') {
            return redirect()->route('inventory.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only pending purchase orders can be edited');
        }
        
        $suppliers = Supplier::all();
        $products = Product::with('inventory')->get();
        $purchaseOrder->load('items');
        
        return view('inventory.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }
    
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status != 'pending') {
            return redirect()->route('inventory.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only pending purchase orders can be edited');
        }
        
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['required', 'date', 'after_or_equal:order_date'],
            'notes' => ['nullable', 'string'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.unit_price' => ['required', 'numeric', 'min:0']
        ]);
        
        // Update purchase order
        $purchaseOrder->update([
            'supplier_id' => $request->supplier_id,
            'order_date' => $request->order_date,
            'expected_delivery_date' => $request->expected_delivery_date,
            'notes' => $request->notes
        ]);
        
        // Delete existing items
        $purchaseOrder->items()->delete();
        
        // Add new items to purchase order
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            
            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['unit_price'],
                'total_price' => $productData['quantity'] * $productData['unit_price']
            ]);
        }
        
        return redirect()->route('inventory.purchase-orders.show', $purchaseOrder)->with('success', 'Purchase order updated successfully');
    }
    
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status != 'pending') {
            return back()->with('error', 'Only pending purchase orders can be approved');
        }
        
        $purchaseOrder->update(['status' => 'approved']);
        return back()->with('success', 'Purchase order approved');
    }
    
    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status != 'approved') {
            return back()->with('error', 'Only approved purchase orders can be marked as received');
        }
        
        // Update inventory for each item
        foreach ($purchaseOrder->items as $item) {
            $inventory = Inventory::where('product_id', $item->product_id)->first();
            if ($inventory) {
                $inventory->increment('quantity', $item->quantity);
            }
        }
        
        $purchaseOrder->update(['status' => 'delivered']);
        return back()->with('success', 'Purchase order marked as delivered and inventory updated');
    }
    
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status == 'delivered' || $purchaseOrder->status == 'cancelled') {
            return back()->with('error', 'Cannot cancel a delivered or already cancelled order');
        }
        
        $purchaseOrder->update(['status' => 'cancelled']);
        return back()->with('success', 'Purchase order cancelled');
    }
    
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status != 'pending') {
            return redirect()->route('inventory.purchase-orders.index')
                ->with('error', 'Only pending purchase orders can be deleted');
        }
        
        $purchaseOrder->delete();
        return redirect()->route('inventory.purchase-orders.index')->with('success', 'Purchase order deleted successfully');
    }
}