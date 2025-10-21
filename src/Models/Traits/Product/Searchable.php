<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\AbstractAttribute;
use Rapidez\Core\Models\Category;
use Rapidez\Core\Models\CategoryProduct;
use Rapidez\Core\Models\EavAttribute;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\Traits\Searchable as ParentSearchable;
use TorMorten\Eventy\Facades\Eventy;

trait Searchable
{
    use ParentSearchable;

    /**
     * {@inheritdoc}
     */
    protected function makeAllSearchableUsing(Builder $query)
    {
        return $query
            ->with(['reviewSummary', 'children'])
            ->withEventyGlobalScopes('index.' . static::getModelName() . '.scopes');
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBeSearchable(): bool
    {
        if (! in_array($this->visibility->value, [
            Product::VISIBILITY_IN_CATALOG,
            Product::VISIBILITY_IN_SEARCH,
            Product::VISIBILITY_BOTH,
        ])) {
            return false;
        }

        $showOutOfStock = (bool) Rapidez::config('cataloginventory/options/show_out_of_stock', 0);
        if (! $showOutOfStock && ! $this->stock->is_in_stock) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function toSearchableArray(): array
    {
        $indexable = Cache::driver('array')->rememberForever('indexable_attribute_codes', fn () => EavAttribute::getCachedIndexable()->pluck($this->getCustomAttributeCode()));
        $keys = $this->customAttributes->keys()->intersect($indexable)->toArray();

        $data = $this->only([
            'entity_id',
            'sku',
            'children',
            'url',
            'in_stock',
            'min_sale_qty',
            'qty_increments',
            ...$keys,
            ...$this->superAttributes->pluck('attribute_code'),
        ]);

        $data['store'] = config('rapidez.store');
        $data['super_attributes'] = $this->superAttributes->keyBy('attribute_id');

        $maxPositions = Cache::driver('array')->rememberForever('max-positions-' . config('rapidez.store'), function () {
            return CategoryProduct::query()
                ->selectRaw('GREATEST(MAX(position), 0) as position')
                ->addSelect('category_id')
                ->groupBy('category_id')
                ->pluck('position', 'category_id');
        });

        foreach ($this->superAttributeValues as $attribute => $values) {
            $data['super_' . $attribute] = $values->pluck('value');
        }

        $data = $this->withCategories($data);

        $data['positions'] = $this->categoryProducts
            ->pluck('position', 'category_id')
            // Turn all positions positive
            ->mapWithKeys(fn ($position, $category_id) => [$category_id => $maxPositions[$category_id] - $position]);

        $data = $this->transformAttributes($data);

        return Eventy::filter('index.' . static::getModelName() . '.data', $data, $this);
    }

    public function transformAttributes(array $data): array
    {
        // TODO: Can this be done directly from AbstractAttribute instead?
        // Would be a lot cleaner if we don't have to manually loop through this.
        return Arr::map($data, function ($value, $key) {
            if ($value instanceof AbstractAttribute) {
                if ($value->value == $value->label) {
                    return $value->value;
                } else {
                    return [
                        'value' => $value->value,
                        'label' => $value->label,
                    ];
                }
            } elseif ($key === 'children') {
                return $value->map(fn ($child) => $this->transformAttributes($child));
            } else {
                return $value;
            }
        });
    }

    /**
     * Add the category paths
     */
    public function withCategories(array $data): array
    {
        $categories = Cache::driver('array')->rememberForever('categories', function () {
            return Category::all()->keyBy('entity_id');
        });

        foreach ($this->breadcrumbCategories as $category) {
            if (! $category) {
                continue;
            }

            $path = array_slice(explode('/', $category->path), 2);
            $level = count($path);

            if ($level < 1) {
                continue;
            }

            for ($i = 1; $i <= $level; $i++) {
                $pathCategories = collect($path)
                    ->take($i)
                    ->map(fn ($id) => $categories[$id]->name ?? null)
                    ->whereNotNull()
                    ->join(' > ');

                $data['category_lvl' . $i][] = $pathCategories;
            }
        }

        foreach ($data as $key => &$value) {
            if (str_starts_with($key, 'category_lvl')) {
                $value = array_values(array_unique($value));
            }
        }

        return $data;
    }

    protected static function indexMapping(): array
    {
        $attributeModel = config('rapidez.models.attribute');

        $attributeTypeMapping = collect(
            $attributeModel::getCachedWhere(function ($attribute) {
                if (in_array($attribute['code'], ['msrp_display_actual_price_type', 'price_view', 'shipment_type', 'status'])) {
                    return false;
                }

                if (in_array($attribute['type'], ['varchar', 'text', 'gallery', 'static'])) {
                    // Types best left up to ES to interpret the type.
                    return false;
                }

                if (! empty($attribute['source_model'])) {
                    // Due to the source model value can be mapped to any type, best to let ES interpret them.
                    return false;
                }

                if ($attribute['input'] === 'select') {
                    // Select means that while the type may be an int, the data won't be an int.
                    return false;
                }

                if ($attribute['listing'] || $attribute['filter'] || $attribute['search'] || $attribute['sorting']) {
                    return true;
                }

                $alwaysInFlat = array_merge(['sku'], Eventy::filter('index.' . static::getModelName() . '.attributes', []));
                if (in_array($attribute['code'], $alwaysInFlat)) {
                    return true;
                }

                return false;
            })
        )
            ->pluck('type', 'code')
            ->map(function ($type) {
                return array_filter([
                    'type' => match ($type) {
                        'int'      => 'integer',
                        'decimal'  => 'double',
                        'datetime' => 'date',
                        default    => null
                    },
                    'format' => match ($type) {
                        'datetime' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||strict_date_optional_time||epoch_millis',
                        default    => null
                    },
                ]);
            })
            ->whereNotNull('type');

        return [
            'properties' => [
                ...$attributeTypeMapping,
                'children' => [
                    'type' => 'flattened',
                ],
                'grouped' => [
                    'type' => 'flattened',
                ],
                'positions' => [
                    'type' => 'flattened',
                ],
            ],
        ];
    }

    public static function synonymFields(): array
    {
        return ['name', 'short_description', 'description'];
    }
}
