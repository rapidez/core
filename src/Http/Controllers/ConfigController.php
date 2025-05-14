<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Category;
use Rapidez\Core\Models\Traits\Searchable;

class ConfigController
{
    public function __invoke()
    {
        $config = array_merge(
            $this->getExposedConfigValues(),
            $this->getConfig(),
        );

        return response()->view(
            view: 'rapidez::layouts.config',
            headers: ['Content-Type' => 'text/javascript'],
            data: ['config' => $config],
        );
    }

    public function getExposedConfigValues(): array
    {
        return Arr::only(
            array_merge_recursive(
                config('rapidez'),
                config('rapidez.frontend'),
            ),
            array_merge(
                config('rapidez.frontend.exposed'),
                ['store_code', 'index', 'searchkit'],
            ),
        );
    }

    public function getConfig(): array
    {
        return [
            'cachekey'     => Cache::rememberForever('cachekey', fn () => md5(Str::random())),
            'translations' => __('rapidez::frontend'),

            'index'                        => $this->getIndexNames(),
            'filterable_attributes'        => $this->getFilterableAttributes(),
            'fragments'                    => $this->getGraphqlQueryFragments(),
            'max_category_level'           => $this->getMaxCategoryLevel(),
            'queries'                      => $this->getGraphqlQueries(),
            'searchkit'                    => $this->getSearchkitConfig(),
            'show_customer_address_fields' => $this->getCustomerAddressFields(),
            'swatches'                     => $this->getSwatches(),

            // Magento config data
            'currency'             => Rapidez::config('currency/options/default'),
            'default_country'      => Rapidez::config('general/country/default', 'NL'),
            'grid_per_page'        => Rapidez::config('catalog/frontend/grid_per_page', 12),
            'grid_per_page_values' => explode(',', Rapidez::config('catalog/frontend/grid_per_page_values', '12,24,36')),
            'locale'               => Rapidez::config('general/locale/code', 'en_US'),
            'recaptcha'            => Rapidez::config('recaptcha_frontend/type_recaptcha_v3/public_key', null, true),
            'redirect_cart'        => (bool) Rapidez::config('checkout/cart/redirect_to_cart'),
            'street_lines'         => Rapidez::config('customer/address/street_lines', 2),
            'show_swatches'        => (bool) Rapidez::config('catalog/frontend/show_swatches_in_product_list'),
            'show_tax'             => in_array(Rapidez::config('tax/display/type', 1), [2, 3]),
        ];
    }

    public function getSwatches(): array
    {
        $optionswatchModel = config('rapidez.models.option_swatch');

        return $optionswatchModel::getCachedSwatchValues();
    }

    public function getIndexNames(): array
    {
        return collect(config('rapidez.models'))
            ->filter(fn ($class) => in_array(Searchable::class, class_uses_recursive($class)))
            ->map(fn ($class) => (new $class)->searchableAs())
            ->toArray();
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
            ->map(fn ($attribute) => [
                ...$attribute,
                'code'      => ($attribute['prefix'] ?? '') . $attribute['code'],
                'base_code' => $attribute['code'],
            ])
            ->sortBy('position')
            ->values()
            ->toArray();
    }

    public function getMaxCategoryLevel(): int
    {
        return Cache::rememberForever('max_category_level_' . config('rapidez.store'), fn () => Category::withoutGlobalScopes()->max('level'));
    }

    public function getGraphqlQueries(): array
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

        $queries = Arr::mapWithKeys($checkoutQueries, fn ($query) => [$query => view('rapidez::checkout.queries.' . $query)->renderOneliner()]);
        $queries['customer'] = view('rapidez::customer.queries.customer')->renderOneliner();

        return $queries;
    }

    public function getGraphqlQueryFragments(): array
    {
        return [
            'cart'    => view('rapidez::cart.queries.fragments.cart')->renderOneliner(),
            'order'   => view('rapidez::checkout.queries.fragments.order')->renderOneliner(),
            'orderV2' => view('rapidez::checkout.queries.fragments.orderV2')->renderOneliner(),
        ];
    }

    public function getSearchkitConfig(): array
    {
        return array_merge(
            config('rapidez.searchkit'),
            [
                'facet_attributes'  => $this->getSearchkitFacetAttributes(),
                'search_attributes' => $this->getSearchkitSearchAttributes(),
                'sorting'           => $this->getSearchkitSorting(),
            ]
        );

    }

    public function getSearchkitFacetAttributes(): array
    {
        // Get the filterable attributes and category levels
        $filterableAttributes = collect($this->getFilterableAttributes())
            ->map(function ($attribute) {
                $isNumeric = $attribute['super'] || in_array($attribute['input'], ['boolean', 'price']);

                return [
                    'attribute' => $attribute['code'],
                    'field'     => $attribute['code'] . ($isNumeric ? '' : '.keyword'),
                    'type'      => $isNumeric ? 'numeric' : 'string',
                ];
            });

        $maxLevel = $this->getMaxCategoryLevel();
        $categoryLevels = collect(array_fill(1, $maxLevel, 'category_lvl'))
            ->map(fn ($value, $key) => [
                'attribute' => $value . $key,
                'field'     => $value . $key . '.keyword',
                'type'      => 'string',
            ]);

        $facetAttributes = $filterableAttributes
            ->concat($categoryLevels)
            ->concat(config('rapidez.searchkit.facet_attributes'));

        return $facetAttributes->toArray();
    }

    public function getSearchkitSearchAttributes(): array
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

        return $searchableAttributes;
    }

    public function getSearchkitSorting(): array
    {
        $attributeModel = config('rapidez.models.attribute');

        // Get all sortable attributes from Magento and any that have been set manually in the config
        $sortableAttributes = $attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['sorting'] || in_array($attribute['code'], array_keys(config('rapidez.searchkit.sorting')));
        });

        // Add `direction` to custom sortings
        foreach (config('rapidez.searchkit.sorting') as $code => $directions) {
            $attribute = collect($sortableAttributes)->search(fn ($attribute) => $attribute['code'] == $code);
            if ($attribute) {
                $sortableAttributes[$attribute]['directions'] = $directions;
            }
        }

        $index = (new (config('rapidez.models.product')))->searchableAs();
        $sortableAttributes = collect($sortableAttributes)
            ->flatMap(fn ($attribute) => Arr::map(($attribute['directions'] ?? null) ?: ['asc', 'desc'], fn ($direction) => [
                'label' => trans_fallback(
                    "rapidez::frontend.sorting.{$attribute['code']}.{$direction}",
                    trans_fallback("rapidez::frontend.{$attribute['code']}", $attribute['code']) . ' ' . trans_fallback("rapidez::frontend.{$direction}", $direction),
                ),
                'field' => $attribute['code'] . ($attribute['input'] == 'text' ? '.keyword' : ''),
                'order' => $direction,
                'value' => "{$index}_{$attribute['code']}_{$direction}",
                'key'   => "_{$attribute['code']}_{$direction}",
            ]));

        // Add default relevance sort
        $sortableAttributes->prepend([
            'label' => __('Relevance'),
            'field' => '_score',
            'order' => 'desc',
            'value' => $index,
            'key'   => 'default',
        ]);

        return $sortableAttributes->keyBy('key')->toArray();
    }
}
