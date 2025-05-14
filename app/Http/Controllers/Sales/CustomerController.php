<?php

// app/Http/Controllers/Sales/CustomerController.php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')->withCount('orders')->paginate(10);
        return view('sales.customers.index', compact('customers'));
    }
    
    public function show(User $customer)
    {
        if ($customer->role != 'customer') {
            abort(404);
        }
        
        $orders = $customer->orders()->with('sale')->latest()->paginate(5);
        return view('sales.customers.show', compact('customer', 'orders'));
    }
    
    public function edit(User $customer)
    {
        if ($customer->role != 'customer') {
            abort(404);
        }
        
        return view('sales.customers.edit', compact('customer'));
    }
    
    public function update(Request $request, User $customer)
    {
        if ($customer->role != 'customer') {
            abort(404);
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($customer->id)],
        ]);
        
        $customer->update($request->only(['name', 'email']));
        
        return redirect()->route('sales.customers.show', $customer)->with('success', 'Customer updated successfully');
    }
}