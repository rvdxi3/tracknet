<?php

// app/Http/Controllers/Website/HomeController.php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->with('inventory')
            ->inRandomOrder()
            ->take(8)
            ->get();
            
        return view('website.home', compact('featuredProducts'));
    }
}