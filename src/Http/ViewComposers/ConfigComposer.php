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
            array_merge_recursive(config('rapidez'), config('rapidez.frontend')),
            array_merge(config('rapidez.frontend.exposed'), ['store_code'])
        );

        Config::set('frontend', array_merge(
            config('frontend') ?: [],
            $exposedFrontendConfigValues
        ));

        $attributeModel = config('rapidez.models.attribute');
        $searchableAttributes = Arr::pluck(
            $attributeModel::getCachedWhere(fn ($attribute) => $attribute['search'] && in_array($attribute['type'], ['text', 'varchar', 'static'])
            ),
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
        Config::set('frontend.searchable', array_merge($searchableAttributes, config('rapidez.indexer.searchable')));
        Config::set('frontend.show_customer_address_fields', $this->getCustomerAddressFields());
        Config::set('frontend.show_tax', (bool) Rapidez::config('tax/display/type', 1));
        Config::set('frontend.grid_per_page', Rapidez::config('catalog/frontend/grid_per_page', 12));
        Config::set('frontend.grid_per_page_values', explode(',', Rapidez::config('catalog/frontend/grid_per_page_values', '12,24,36')));
        Config::set('frontend.queries.cart', view('rapidez::cart.queries.cart')->renderOneliner());
    }

    public function getCustomerAddressFields()
    {
        return [
            'prefix'      => strlen(Rapidez::config('customer/address/prefix_options', '')) ? Rapidez::config('customer/address/prefix_show', 'opt') : 'opt',
            'firstname'   => 'req',
            'middlename'  => Rapidez::config('customer/address/middlename_show', 0) ? 'opt' : false,
            'lastname'    => 'req',
            'suffix'      => strlen(Rapidez::config('customer/address/suffix_options', '')) ? Rapidez::config('customer/address/suffix_show', 'opt') : 'opt',
            'postcode'    => 'req',
            'housenumber' => Rapidez::config('customer/address/street_lines', 3) >= 2 ? 'req' : false,
            'addition'    => Rapidez::config('customer/address/street_lines', 3) >= 3 ? 'opt' : false,
            'street'      => 'req',
            'city'        => 'req',
            'country_id'  => 'req',
            'telephone'   => Rapidez::config('customer/address/telephone_show', 'req'),
            'company'     => Rapidez::config('customer/address/company_show', 'opt'),
            'vat_id'      => Rapidez::config('customer/address/taxvat_show', 'opt'),
            'fax'         => Rapidez::config('customer/address/fax_show', 'opt'),
        ];
    }
}
