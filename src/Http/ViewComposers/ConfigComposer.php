<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Rapidez\Core\Facades\Rapidez;

class ConfigComposer
{
    public function compose(View $view)
    {
        $exposedFrontendConfigValues = Arr::only(
            config('rapidez'),
            array_merge(config('rapidez.exposed'), ['store_code'])
        );

        Config::set('frontend', array_merge(
            config('frontend') ?: [],
            $exposedFrontendConfigValues
        ));

        $attributeModel = config('rapidez.models.attribute');
        $searchableAttributes = Arr::pluck(
            $attributeModel::getCachedWhere(fn ($attribute) => $attribute['search']),
            'search_weight',
            'code'
        );

        Config::set('frontend.locale', Rapidez::config('general/locale/code', 'en_US'));
        Config::set('frontend.default_country', Rapidez::config('general/country/default', 'NL'));
        Config::set('frontend.currency', Rapidez::config('currency/options/default'));
        Config::set('frontend.cachekey', Cache::rememberForever('cachekey', fn () => md5(Str::random())));
        Config::set('frontend.redirect_cart', (bool) Rapidez::config('checkout/cart/redirect_to_cart'));
        Config::set('frontend.show_swatches', (bool) Rapidez::config('catalog/frontend/show_swatches_in_product_list'));
        Config::set('frontend.translations', __('rapidez::frontend'));
        Config::set('frontend.recaptcha', Rapidez::config('recaptcha_frontend/type_recaptcha_v3/public_key', null, true));
        Config::set('frontend.searchable', array_merge($searchableAttributes, config('rapidez.searchable')));
        Config::set('frontend.customer_fields_show', $this->getCustomerFields());
        Config::set('frontend.grid_per_page', Rapidez::config('catalog/frontend/grid_per_page', 12));
        Config::set('frontend.grid_per_page_values', explode(',', Rapidez::config('catalog/frontend/grid_per_page_values', '12,24,36')));
        Config::set('frontend.tax', $this->getTaxConfiguration());
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

    public function getTaxConfiguration()
    {
        return [
            'values' => (object) Rapidez::getTaxTable(),
            'groups' => (object) Rapidez::getTaxGroups(),
            'calculation' => [
                'price_includes_tax' => boolval(Rapidez::config('tax/calculation/price_includes_tax', 0)),
                'base_subtotal_should_include_tax' => boolval(Rapidez::config('tax/calculation/base_subtotal_should_include_tax', 1)),
                'algorithm' => Rapidez::config('tax/calculation/algorithm', 'TOTAL_BASE_CALCULATION'),
                'apply_after_discount' => boolval(Rapidez::config('tax/calculation/apply_after_discount', 1)),
                'apply_tax_on' => Rapidez::config('tax/calculation/apply_tax_on', 0),
                'based_on' => Rapidez::config('tax/calculation/based_on', 'shipping'),
                'cross_border_trade_enabled' => boolval(Rapidez::config('tax/calculation/cross_border_trade_enabled', 0)),
                'discount_tax' => boolval(Rapidez::config('tax/calculation/discount_tax', 0)),
                'shipping_includes_tax' => boolval(Rapidez::config('tax/calculation/shipping_includes_tax', 0))
            ],
            'display' => [
                'catalog' => Rapidez::config('tax/display/type', 1),
                'shipping' => Rapidez::config('tax/display/shipping', 1),
                'cart_price' => Rapidez::config('tax/cart_display/price', 1),
                'cart_shipping' => Rapidez::config('tax/cart_display/shipping', 1),
                'cart_subtotal' => Rapidez::config('tax/cart_display/subtotal', 1)
            ],
            'defaults' => [
                'country' => Rapidez::config('tax/defaults/country', 'US'),
                'postcode' => Rapidez::config('tax/defaults/postcode', null),
                'region' => Rapidez::config('tax/defaults/region', 0)
            ]
        ];
    }
}
