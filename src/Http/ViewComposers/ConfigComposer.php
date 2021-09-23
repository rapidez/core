<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use TorMorten\Eventy\Facades\Eventy;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConfigComposer
{
    public function compose(View $view)
    {
        $exposedFrontendConfigValues = Arr::only(
            config('rapidez'),
            array_merge(config('rapidez.exposed'), ['store_code'])
        );

        $config = Eventy::filter('config.exposed', []);
        $exposedFrontendConfigValues = array_merge($exposedFrontendConfigValues, $config);

        config(['frontend' => array_merge(
            config('frontend') ?: [],
            $exposedFrontendConfigValues
        )]);

        $configModel = config('rapidez.models.config');
        $attributeModel = config('rapidez.models.attribute');

        config(['frontend.locale' => $configModel::getCachedByPath('general/locale/code', 'en_US')]);
        config(['frontend.currency' => $configModel::getCachedByPath('currency/options/default')]);
        config(['frontend.cachekey' => Cache::rememberForever('cachekey', fn () => md5(Str::random()))]);
        config(['frontend.redirect_cart' => (bool) $configModel::getCachedByPath('checkout/cart/redirect_to_cart')]);
        config(['frontend.translations' => __('rapidez::frontend')]);

        config(['frontend.searchable' => Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['search'];
        }), 'search_weight', 'code')]);
    }
}
