<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Rapidez\Core\Facades\Rapidez;
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

        $attributeModel = config('rapidez.models.attribute');
        $searchableAttributes = Arr::pluck(
            $attributeModel::getCachedWhere(fn ($attribute) => $attribute['search']),
            'search_weight',
            'code'
        );

        config(['frontend.locale' => Rapidez::config('general/locale/code', 'en_US')]);
        config(['frontend.default_country' => Rapidez::config('general/country/default', 'NL')]);
        config(['frontend.currency' => Rapidez::config('currency/options/default')]);
        config(['frontend.cachekey' => Cache::rememberForever('cachekey', fn () => md5(Str::random()))]);
        config(['frontend.redirect_cart' => (bool) Rapidez::config('checkout/cart/redirect_to_cart')]);
        config(['frontend.show_swatches' => (bool) Rapidez::config('catalog/frontend/show_swatches_in_product_list')]);
        config(['frontend.translations' => __('rapidez::frontend')]);
        config(['frontend.recaptcha' => Config::getCachedByPath('recaptcha_frontend/type_recaptcha_v3/public_key', null, true)]);
        config(['frontend.searchable' => array_merge($searchableAttributes, config('rapidez.searchable'))]);
        config(['frontend.customer_fields_show' => $this->getCustomerFields()]);
        config(['frontend.grid_per_page' => Rapidez::config('catalog/frontend/grid_per_page', 12)]);
        config(['frontend.grid_per_page_values' => Rapidez::config('catalog/frontend/grid_per_page_values', '[12, 24]')]);
    }

    public function getCustomerFields()
    {
        return [
            'firstname'   => 'req',
            'middlename'  => Rapidez::config('customer/address/middlename_show', 0) ? 'opt' : false,
            'lastname'    => 'req',
            'postcode'    => 'req',
            'housenumber' => Rapidez::config('customer/address/street_lines', 3) >= 2 ? 'req' : false,
            'addition'    => Rapidez::config('customer/address/street_lines', 3) >= 3 ? 'opt' : false,
            'street'      => 'req',
            'city'        => 'req',
            'country_id'  => 'req',
            'telephone'   => Rapidez::config('customer/address/telephone_show', 'req'),
            'company'     => Rapidez::config('customer/address/company_show', 'opt'),
        ];
    }
}
