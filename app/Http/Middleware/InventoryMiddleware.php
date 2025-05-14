<?php

// app/Http/Middleware/InventoryMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isInventory()) {
            return $next($request);
        }

        return redirect('/')->with('error', 'You do not have inventory department access');
    }
}
