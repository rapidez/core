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
            ->configureSearchkitFacetAttributes()
            ->configureSearchkitSearchAttributes()
            ->configureSearchkitSorting();

        $exposedFrontendConfigValues = Arr::only(
            array_merge_recursive(
                config('rapidez'),
                config('rapidez.frontend'),
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
            'max_category_level'           => Cache::rememberForever('max_category_level', fn () => Category::withoutGlobalScopes()->max('level')),
            'filterable_attributes'        => $this->getFilterableAttributes(),
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

    public function getFilterableAttributes(): array
    {
        $attributes = config('rapidez.models.attribute')::getCachedWhere(function ($attribute) {
            return $attribute['filter'] || $attribute['sorting'];
        });

        return collect($attributes)
            ->map(fn($attribute) => [
                ...$attribute,
                'code' => $attribute['prefix'] . $attribute['code'],
                'base_code' => $attribute['code'],
            ])
            ->sortBy('position')
            ->values()
            ->toArray();
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

    public function configureSearchkitFacetAttributes(): self
    {
        $attributeModel = config('rapidez.models.attribute');

        // Get the filterable attributes and category levels
        $filterableAttributes = collect($this->getFilterableAttributes())
            ->map(function ($attribute) {
                $isNumeric = $attribute['super'] || in_array($attribute['input'], ['boolean', 'price']);
                return [
                    'attribute' => $attribute['code'],
                    'field' => $attribute['code'] . ($isNumeric ? '' : '.keyword'),
                    'type' => $isNumeric ? 'numeric' : 'string'
                ];
            });

        $maxLevel = Cache::rememberForever('max_category_level', fn () => Category::withoutGlobalScopes()->max('level'));
        $categoryLevels = collect(array_fill(1, $maxLevel, 'category_lvl'))
            ->map(fn ($value, $key) => [
                'attribute' => $value . $key,
                'field' => $value . $key . '.keyword',
                'type' => 'string'
            ]);

        $facetAttributes = $filterableAttributes
            ->concat($categoryLevels)
            ->concat(config('rapidez.searchkit.facet_attributes'));

        Config::set('rapidez.searchkit.facet_attributes', $facetAttributes->toArray());

        return $this;
    }

    public function configureSearchkitSearchAttributes(): self
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

    public function configureSearchkitSorting(): self
    {
        $attributeModel = config('rapidez.models.attribute');

        // Get all sortable attributes from Magento and any that have been set manually in the config
        $sortableAttributes = $attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['sorting'] || in_array($attribute['code'], array_keys(config('rapidez.searchkit.sorting')));
        });

        // Add `direction` to custom sortings
        foreach(config('rapidez.searchkit.sorting') as $code => $directions) {
            $attribute = collect($sortableAttributes)->search(fn($attribute) => $attribute['code'] == $code);
            if ($attribute) {
                $sortableAttributes[$attribute]['directions'] = $directions;
            }
        }

        $index = (new (config('rapidez.models.product')))->searchableAs();
        $sortableAttributes = collect($sortableAttributes)
            ->flatMap(fn ($attribute) => Arr::map(($attribute['directions'] ?? null) ?: ['asc', 'desc'], fn($direction) => [
                'label' => __("{$attribute['code']} {$direction}"),
                'field' => $attribute['code'] . ($attribute['input'] == 'text' ? '.keyword' : ''),
                'order' => $direction,
                'value' => "{$index}_{$attribute['code']}_{$direction}",
                'key' => "_{$attribute['code']}_{$direction}",
            ]));

        // Add default relevance sort
        $sortableAttributes->prepend([
            'label' => 'relevance',
            'field' => '_score',
            'order' => 'desc',
            'value' => $index,
            'key' => 'default',
        ]);

        Config::set('rapidez.searchkit.sorting', $sortableAttributes->keyBy('key')->toArray());

        return $this;
    }
}
