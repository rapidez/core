<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\TaxCalculation;

class ConfigComposer
{
    public function compose(View $view)
    {
        $exposedFrontendConfigValues = Arr::only(
            array_merge_recursive(config('rapidez'), config('rapidez.frontend')),
            array_merge(config('rapidez.frontend.exposed'), ['store_code'])
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
        $customerGroupId = auth('magento-customer')->user()?->group_id ?? 0;
        $values = Cache::remember('tax-configuration-' . $customerGroupId, 3600, function () use ($customerGroupId) {
            return TaxCalculation::select('tax_country_id', 'tax_region_id', 'tax_postcode', 'rate', 'product_tax_class_id')
                ->whereHas('customerGroups', fn ($query) => $query->where('customer_group_id', $customerGroupId))
                ->get()
                ->groupBy('product_tax_class_id');
        });

        return [
            'rates'       => $values,
            'calculation' => [
                'price_includes_tax' => boolval(Rapidez::config('tax/calculation/price_includes_tax', 0)),
                'based_on'           => Rapidez::config('tax/calculation/based_on', 'shipping'),
            ],
            'display' => [
                'catalog'       => Rapidez::config('tax/display/type', 1),
                'shipping'      => Rapidez::config('tax/display/shipping', 1),
                'cart_price'    => Rapidez::config('tax/cart_display/price', 1),
                'cart_shipping' => Rapidez::config('tax/cart_display/shipping', 1),
                'cart_subtotal' => Rapidez::config('tax/cart_display/subtotal', 1),
            ],
            'defaults' => [
                'country_id' => Rapidez::config('tax/defaults/country', 'US'),
                'postcode'   => Rapidez::config('tax/defaults/postcode', null),
                'region_id'  => Rapidez::config('tax/defaults/region', 0),
            ],
        ];
    }
}
