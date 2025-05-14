<?php

// app/Http/Middleware/SalesMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isSales()) {
            return $next($request);
        }

        return redirect('/')->with('error', 'You do not have sales department access');
    }
}