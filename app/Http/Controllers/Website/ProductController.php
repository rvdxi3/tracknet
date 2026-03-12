<?php

// app/Http/Controllers/Website/ProductController.php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['category', 'inventory']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%')
                  ->orWhere('sku', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Sorting
        switch ($request->sort) {
            case 'name_asc':   $query->orderBy('name', 'asc');   break;
            case 'name_desc':  $query->orderBy('name', 'desc');  break;
            case 'price_asc':  $query->orderBy('price', 'asc');  break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            default:           $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();

        return view('website.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load('inventory', 'category');

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'inventory'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('website.products.show', compact('product', 'relatedProducts'));
    }

    public function category(Category $category)
    {
        $products = $category->products()
            ->with(['category', 'inventory'])
            ->paginate(12)
            ->withQueryString();
        $categories = Category::withCount('products')->get();
        $currentCategory = $category;

        return view('website.products.index', compact('products', 'categories', 'currentCategory'));
    }
}
