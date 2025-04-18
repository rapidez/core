<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Category;

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
            $this->getConfig(),
        ));

        Event::dispatch('rapidez:frontend-config-composed');
    }

    public function getConfig(): array
    {
        return [
            'locale'               => Rapidez::config('general/locale/code'),
            'default_country'      => Rapidez::config('general/country/default', 'NL'),
            'currency'             => Rapidez::config('currency/options/default'),
            'cachekey'             => Cache::rememberForever('cachekey', fn () => md5(Str::random())),
            'redirect_cart'        => (bool) Rapidez::config('checkout/cart/redirect_to_cart'),
            'show_swatches'        => (bool) Rapidez::config('catalog/frontend/show_swatches_in_product_list'),
            'translations'         => __('rapidez::frontend'),
            'recaptcha'            => Rapidez::config('recaptcha_frontend/type_recaptcha_v3/public_key', null, true),
            'street_lines'         => Rapidez::config('customer/address/street_lines'),
            'show_tax'             => in_array(Rapidez::config('tax/display/type'), [2, 3]),
            'grid_per_page'        => Rapidez::config('catalog/frontend/grid_per_page'),
            'grid_per_page_values' => explode(',', Rapidez::config('catalog/frontend/grid_per_page_values')),
            'max_category_level'   => Cache::rememberForever('max_category_level', fn () => Category::withoutGlobalScopes()->max('level')),

            // TODO: For the products we've the `rapidez.index` config
            // set from the `src/Rapidez.php` which is accessible
            // in the frontend with `config.index`, maybe we
            // should change that to `config.index.TYPE`?
            'index_prefix' => config('scout.prefix'),
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
