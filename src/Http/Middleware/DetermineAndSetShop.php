<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rapidez\Core\Facades\Rapidez;

class DetermineAndSetShop
{
    public function handle(Request $request, Closure $next): mixed
    {
        // Set the store based on MAGE_RUN_CODE.
        $storeCode = $request->has('_store') && ! app()->isProduction()
            ? $request->get('_store')
            : $request->server('MAGE_RUN_CODE');

        $store = Rapidez::getStore($storeCode ?: config('rapidez.store'));

        Rapidez::setStore($store);

        return $next($request);
    }
}
