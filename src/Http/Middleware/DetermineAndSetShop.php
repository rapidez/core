<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Store;

class DetermineAndSetShop
{
    public function handle($request, Closure $next)
    {
        // Set the store based on MAGE_RUN_CODE.
        $storeCode = $request->has('_store') && !app()->isProduction()
            ? $request->get('_store')
            : $request->server('MAGE_RUN_CODE');

        $store = $storeCode ? Rapidez::getStore($storeCode) : Rapidez::getStore(config('rapidez.store'));

        Rapidez::setStore($store);

        return $next($request);
    }
}
