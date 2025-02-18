<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Rapidez\Core\Facades\Rapidez;

// TODO: Can we improve anything in this file?
// It doesn't feel very clean currently.
class ConfigComposer
{
    public function compose(View $view)
    {
        $this
            ->exposeGraphqlQueries()
            ->configureSearchkit();

        $exposedFrontendConfigValues = Arr::only(
            array_merge_recursive(
                config('rapidez'),
                config('rapidez.frontend'),
                config('rapidez.searchkit'),
            ),
            array_merge(
                config('rapidez.frontend.exposed'),
                ['store_code', 'index', 'searchkit'],
            ),
        );

        Config::set('frontend', array_merge(
            config('frontend') ?: [],
            $exposedFrontendConfigValues,
            $this->getConfig()
        ));

        Event::dispatch('rapidez:frontend-config-composed');
    }

    public function getConfig(): array
    {
        return [
            'locale'                       => Rapidez::config('general/locale/code', 'en_US'),
            'default_country'              => Rapidez::config('general/country/default', 'NL'),
            'currency'                     => Rapidez::config('currency/options/default'),
            'cachekey'                     => Cache::rememberForever('cachekey', fn () => md5(Str::random())),
            'redirect_cart'                => (bool) Rapidez::config('checkout/cart/redirect_to_cart'),
            'show_swatches'                => (bool) Rapidez::config('catalog/frontend/show_swatches_in_product_list'),
            'translations'                 => __('rapidez::frontend'),
            'recaptcha'                    => Rapidez::config('recaptcha_frontend/type_recaptcha_v3/public_key', null, true),
            'show_customer_address_fields' => $this->getCustomerAddressFields(),
            'street_lines'                 => Rapidez::config('customer/address/street_lines', 2),
            'show_tax'                     => (bool) Rapidez::config('tax/display/type', 1),
            'grid_per_page'                => Rapidez::config('catalog/frontend/grid_per_page', 12),
            'grid_per_page_values'         => explode(',', Rapidez::config('catalog/frontend/grid_per_page_values', '12,24,36')),
            // TODO: For the products we've the `rapidez.index` config
            // set from the `src/Rapidez.php` which is accessible
            // in the frontend with `config.index`, maybe we
            // should change that to `config.index.TYPE`?
            'index_prefix'                 => config('scout.prefix'),
        ];
    }

    public function getCustomerAddressFields(): array
    {
        return [
            'prefix'      => strlen(Rapidez::config('customer/address/prefix_options', '')) ? Rapidez::config('customer/address/prefix_show', 'opt') : 'opt',
            'firstname'   => 'req',
            'middlename'  => Rapidez::config('customer/address/middlename_show', 0) ? 'opt' : false,
            'lastname'    => 'req',
            'suffix'      => strlen(Rapidez::config('customer/address/suffix_options', '')) ? Rapidez::config('customer/address/suffix_show', 'opt') : 'opt',
            'postcode'    => 'req',
            'housenumber' => Rapidez::config('customer/address/street_lines', 2) >= 2 ? 'req' : false,
            'addition'    => Rapidez::config('customer/address/street_lines', 2) >= 3 ? 'opt' : false,
            'street'      => 'req',
            'city'        => 'req',
            'country_id'  => 'req',
            'telephone'   => Rapidez::config('customer/address/telephone_show', 'req'),
            'company'     => Rapidez::config('customer/address/company_show', 'opt'),
            'vat_id'      => Rapidez::config('customer/address/taxvat_show', 'opt'),
            'fax'         => Rapidez::config('customer/address/fax_show', 'opt'),
        ];
    }

    public function exposeGraphqlQueries(): self
    {
        $checkoutQueries = [
            'setGuestEmailOnCart',
            'setNewShippingAddressesOnCart',
            'setExistingShippingAddressesOnCart',
            'setNewBillingAddressOnCart',
            'setExistingBillingAddressOnCart',
            'setShippingMethodsOnCart',
            'setPaymentMethodOnCart',
            'placeOrder',
        ];

        // TODO: Maybe limit this to just the checkout pages?
        foreach ($checkoutQueries as $checkoutQuery) {
            Config::set(
                'frontend.queries.' . $checkoutQuery,
                view('rapidez::checkout.queries.' . $checkoutQuery)->renderOneliner()
            );
        }

        Config::set(
            'frontend.queries.customer',
            view('rapidez::customer.queries.customer')->renderOneliner()
        );

        Config::set('frontend.fragments', [
            'cart'    => view('rapidez::cart.queries.fragments.cart')->renderOneliner(),
            'order'   => view('rapidez::checkout.queries.fragments.order')->renderOneliner(),
            'orderV2' => view('rapidez::checkout.queries.fragments.orderV2')->renderOneliner(),
        ]);

        return $this;
    }

    public function configureSearchkit(): self
    {
        $attributeModel = config('rapidez.models.attribute');

        // Get all searchable attributes from Magento.
        $searchableAttributes = $attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['search']
                && in_array($attribute['type'], ['text', 'varchar', 'static']);
        });

        // Map and merge them with the config.
        $searchableAttributes = collect($searchableAttributes)->map(fn ($attribute) => [
            'field'  => $attribute['code'],
            'weight' => $attribute['search_weight'],
        ])->merge(config('rapidez.searchkit.search_attributes'))->values()->toArray();

        Config::set('rapidez.searchkit.search_attributes', $searchableAttributes);

        return $this;
    }
}
