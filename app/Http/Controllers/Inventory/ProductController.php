<?php

// app/Http/Controllers/Inventory/ProductController.php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'inventory'])->paginate(10);
        return view('inventory.products.index', compact('products'));
    }
    
    public function create()
    {
        $categories = Category::all();
        return view('inventory.products.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:products'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'initial_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0']
        ]);
        
        $product = new Product($request->except(['image', 'initial_quantity', 'low_stock_threshold']));
        $product->is_featured = $request->has('is_featured');
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }
        
        $product->save();
        
        // Create inventory record
        Inventory::create([
            'product_id' => $product->id,
            'quantity' => $request->initial_quantity,
            'low_stock_threshold' => $request->low_stock_threshold
        ]);
        
        return redirect()->route('inventory.products.index')->with('success', 'Product created successfully');
    }
    
    public function show(Product $product)
    {
        return view('inventory.products.show', compact('product'));
    }
    
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('inventory.products.edit', compact('product', 'categories'));
    }
    
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'low_stock_threshold' => ['required', 'integer', 'min:0']
        ]);
        
        $product->fill($request->except(['image', 'low_stock_threshold']));
        $product->is_featured = $request->has('is_featured');
        
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }
        
        $product->save();
        
        // Update inventory threshold
        $product->inventory->update([
            'low_stock_threshold' => $request->low_stock_threshold
        ]);
        
        return redirect()->route('inventory.products.index')->with('success', 'Product updated successfully');
    }
    
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        return redirect()->route('inventory.products.index')->with('success', 'Product deleted successfully');
    }
}