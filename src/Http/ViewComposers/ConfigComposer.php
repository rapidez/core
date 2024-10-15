<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
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
            $exposedFrontendConfigValues,
            $this->getConfig()
        ));

        Config::set('frontend.queries', [
            'customer'                           => view('rapidez::customer.queries.customer')->renderOneliner(),
            'setGuestEmailOnCart'                => view('rapidez::checkout.queries.setGuestEmailOnCart')->renderOneliner(),
            'setNewShippingAddressesOnCart'      => view('rapidez::checkout.queries.setNewShippingAddressesOnCart')->renderOneliner(),
            'setExistingShippingAddressesOnCart' => view('rapidez::checkout.queries.setExistingShippingAddressesOnCart')->renderOneliner(),
            'setNewBillingAddressOnCart'         => view('rapidez::checkout.queries.setNewBillingAddressOnCart')->renderOneliner(),
            'setExistingBillingAddressOnCart'    => view('rapidez::checkout.queries.setExistingBillingAddressOnCart')->renderOneliner(),
            'setShippingMethodsOnCart'           => view('rapidez::checkout.queries.setShippingMethodsOnCart')->renderOneliner(),
            'setPaymentMethodOnCart'             => view('rapidez::checkout.queries.setPaymentMethodOnCart')->renderOneliner(),
            'placeOrder'                         => view('rapidez::checkout.queries.placeOrder')->renderOneliner(),
        ]);

        Config::set('frontend.fragments', [
            'cart'                               => view('rapidez::cart.queries.fragments.cart')->renderOneliner(),
            'order'                              => view('rapidez::checkout.queries.fragments.order')->renderOneliner(),
            'orderV2'                            => view('rapidez::checkout.queries.fragments.orderV2')->renderOneliner(),
        ]);

        Event::dispatch('rapidez:frontend-config-composed');
    }

    public function getConfig() : array
    {
        $attributeModel = config('rapidez.models.attribute');
        $searchableAttributes = Arr::pluck(
            $attributeModel::getCachedWhere(fn ($attribute) => $attribute['search'] && in_array($attribute['type'], ['text', 'varchar', 'static'])
            ),
            'search_weight',
            'code'
        );

        return [
            'locale' => Rapidez::config('general/locale/code', 'en_US'),
            'default_country' => Rapidez::config('general/country/default', 'NL'),
            'currency' => Rapidez::config('currency/options/default'),
            'cachekey' => Cache::rememberForever('cachekey', fn () => md5(Str::random())),
            'redirect_cart' => (bool) Rapidez::config('checkout/cart/redirect_to_cart'),
            'show_swatches' => (bool) Rapidez::config('catalog/frontend/show_swatches_in_product_list'),
            'translations' => __('rapidez::frontend'),
            'recaptcha' => Rapidez::config('recaptcha_frontend/type_recaptcha_v3/public_key', null, true),
            'searchable' => array_merge($searchableAttributes, config('rapidez.indexer.searchable')),
            'show_customer_address_fields' => $this->getCustomerAddressFields(),
            'show_tax' => (bool) Rapidez::config('tax/display/type', 1),
            'grid_per_page' => Rapidez::config('catalog/frontend/grid_per_page', 12),
            'grid_per_page_values' => explode(',', Rapidez::config('catalog/frontend/grid_per_page_values', '12,24,36')),
         ];
    }

    public function getCustomerAddressFields() : array
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
