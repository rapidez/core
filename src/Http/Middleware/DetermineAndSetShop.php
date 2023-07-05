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
        $storeCode = $request->has('_store') && ! app()->isProduction()
            ? $request->get('_store')
            : $request->server('MAGE_RUN_CODE');

        $store = Rapidez::getStore($storeCode ?: config('rapidez.store'));

        Rapidez::setStore($store);
        config()->set('frontend.base_url', url('/'));

        return $next($request);
    }
}
