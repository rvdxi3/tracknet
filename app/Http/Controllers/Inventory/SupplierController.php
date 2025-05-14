<?php

// app/Http/Controllers/Inventory/SupplierController.php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(10);
        return view('inventory.suppliers.index', compact('suppliers'));
    }
    
    public function create()
    {
        return view('inventory.suppliers.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string']
        ]);
        
        Supplier::create($request->all());
        
        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier created successfully');
    }
    
    public function show(Supplier $supplier)
    {
        $purchaseOrders = $supplier->purchaseOrders()->latest()->paginate(5);
        return view('inventory.suppliers.show', compact('supplier', 'purchaseOrders'));
    }
    
    public function edit(Supplier $supplier)
    {
        return view('inventory.suppliers.edit', compact('supplier'));
    }
    
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string']
        ]);
        
        $supplier->update($request->all());
        
        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier updated successfully');
    }
    
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier deleted successfully');
    }
}