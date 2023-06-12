<?php

namespace Rapidez\Core\Http\Middleware;

use Closure;

class DetermineAndSetShop
{
    public function handle($request, Closure $next)
    {
        $storeModel = config('rapidez.models.store');

        // Set the store based on MAGE_RUN_CODE.
        if ($storeCode = $request->has('_store') && !app()->isProduction() ? $request->get('_store') : $request->server('MAGE_RUN_CODE')) {
            $store = $storeModel::getCachedWhere(function ($store) use ($storeCode) {
                return $store['code'] == $storeCode;
            });
        }

        // Find the store code and website by the default store id.
        if (!isset($store)) {
            $store = $storeModel::getCachedWhere(function ($store) {
                return $store['store_id'] == config('rapidez.store');
            });
        }

        config()->set('rapidez.store', $store['store_id']);
        config()->set('rapidez.store_code', $store['code']);
        config()->set('rapidez.website', $store['website_id']);
        config()->set('rapidez.website_code', $store['website_code']);
        config()->set('rapidez.root_category_id', $store['root_category_id']);

        return $next($request);
    }
}
