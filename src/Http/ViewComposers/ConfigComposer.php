<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Rapidez\Core\Models\Config;

class ConfigComposer
{
    public function compose(View $view)
    {
        $exposedFrontendConfigValues = Arr::only(
            config('rapidez'),
            array_merge(config('rapidez.exposed'), ['store_code'])
        );

        config(['frontend' => array_merge(
            config('frontend') ?: [],
            $exposedFrontendConfigValues
        )]);

        $configModel = config('rapidez.models.config');
        $attributeModel = config('rapidez.models.attribute');
        $searchableAttributes = Arr::pluck(
            $attributeModel::getCachedWhere(fn ($attribute) => $attribute['search']),
            'search_weight',
            'code'
        );

        config(['frontend.locale' => $configModel::getCachedByPath('general/locale/code', 'en_US')]);
        config(['frontend.default_country' => $configModel::getCachedByPath('general/country/default', 'NL')]);
        config(['frontend.currency' => $configModel::getCachedByPath('currency/options/default')]);
        config(['frontend.cachekey' => Cache::rememberForever('cachekey', fn () => md5(Str::random()))]);
        config(['frontend.redirect_cart' => (bool) $configModel::getCachedByPath('checkout/cart/redirect_to_cart')]);
        config(['frontend.show_swatches' => (bool) $configModel::getCachedByPath('catalog/frontend/show_swatches_in_product_list')]);
        config(['frontend.translations' => __('rapidez::frontend')]);
        config(['frontend.recaptcha' => Config::getCachedByPath('recaptcha_frontend/type_recaptcha_v3/public_key', null, true)]);
        config(['frontend.searchable' => array_merge($searchableAttributes, config('rapidez.searchable'))]);
    }
}
