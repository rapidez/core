<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckStoreCode
{
    public function handle(Request $request, Closure $next, ...$stores)
    {
        if (!in_array(config('rapidez.store_code'), $stores)) {
            abort(404);
        }

        return $next($request);
    }
}
