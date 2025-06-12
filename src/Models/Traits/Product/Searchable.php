<?php

namespace Rapidez\Core\Models\Traits\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Category;
use Rapidez\Core\Models\CategoryProduct;
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
        return $query->selectOnlyIndexable()
            ->with(['categoryProducts', 'reviewSummary'])
            ->withEventyGlobalScopes('index.' . static::getModelName() . '.scopes');
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBeSearchable(): bool
    {
        if (! in_array($this->visibility, [
            Product::VISIBILITY_IN_CATALOG,
            Product::VISIBILITY_IN_SEARCH,
            Product::VISIBILITY_BOTH,
        ])) {
            return false;
        }

        $showOutOfStock = (bool) Rapidez::config('cataloginventory/options/show_out_of_stock', 0);
        if (! $showOutOfStock && ! $this->in_stock) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function toSearchableArray(): array
    {
        $data = $this->toArray();

        $data['store'] = config('rapidez.store');

        $maxPositions = Cache::driver('array')->rememberForever('max-positions-' . config('rapidez.store'), function () {
            return CategoryProduct::query()
                ->selectRaw('GREATEST(MAX(position), 0) as position')
                ->addSelect('category_id')
                ->groupBy('category_id')
                ->pluck('position', 'category_id');
        });

        foreach ($this->super_attributes ?: [] as $superAttribute) {
            $data['super_' . $superAttribute->code] = $superAttribute->text_swatch || $superAttribute->visual_swatch
                ? array_keys((array) $this->{'super_' . $superAttribute->code})
                : Arr::pluck($this->{'super_' . $superAttribute->code} ?: [], 'label');
        }

        $data = $this->withCategories($data);

        $data['positions'] = $this->categoryProducts
            ->pluck('position', 'category_id')
            // Turn all positions positive
            ->mapWithKeys(fn ($position, $category_id) => [$category_id => $maxPositions[$category_id] - $position]);

        // TODO: This should be dynamic!
        $data['semantic'] = implode(' - ', [
            'Product name: ' . $data['name'],
            'SKU: ' . $data['sku'],
            'Price: ' . $data['price'] . ' euro',
            'Activity: ' . implode(', ', $data['activity'] ?? []),
            'Material: ' . implode(', ', $data['material'] ?? []),
            'Style general: ' . implode(', ', $data['style_general'] ?? []),
            'Style bottom: ' . implode(', ', $data['style_bottom'] ?? []),
            'Climate: ' . implode(', ', $data['climate'] ?? []),
            'Pattern: ' . implode(', ', $data['pattern'] ?? []),
            'Gender: ' . implode(', ', $data['gender'] ?? []),
            'Description: ' . strip_tags($data['description']),
        ]);

        return Eventy::filter('index.' . static::getModelName() . '.data', $data, $this);
    }

    /**
     * Add the category paths
     */
    public function withCategories(array $data): array
    {
        $categories = Cache::driver('array')->rememberForever('categories-' . config('rapidez.store'), function () {
            return Category::withEventyGlobalScopes('index.' . config('rapidez.models.category')::getModelName() . '.scopes')
                ->where('catalog_category_flat_store_' . config('rapidez.store') . '.entity_id', '<>', config('rapidez.root_category_id'))
                ->pluck('name', 'entity_id');
        });

        foreach ($data['category_paths'] as $categoryPath) {
            $paths = explode('/', $categoryPath);
            $paths = array_slice($paths, array_search(config('rapidez.root_category_id'), $paths) + 1);

            $categoryHierarchy = [];
            $currentPath = '';

            foreach ($paths as $categoryId) {
                if (isset($categories[$categoryId])) {
                    $currentPath .= ($currentPath ? ' > ' : '') . $categories[$categoryId];
                    $categoryHierarchy[] = $currentPath;
                }
            }

            foreach ($categoryHierarchy as $level => $category) {
                $data['category_lvl' . ($level + 1)][] = $category;
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
        return [
            'properties' => [
                'price' => [
                    'type' => 'double',
                ],
                'children' => [
                    'type' => 'flat_object',
                ],
                'grouped' => [
                    'type' => 'flat_object',
                ],
                'positions' => [
                    'type' => 'flat_object',
                ],
                'semantic_embedding' => [
                    'type'      => 'knn_vector',
                    'dimension' => 512,
                    'method'    => [
                        'name'       => 'hnsw',
                        'space_type' => 'innerproduct',
                        'engine'     => 'nmslib',
                    ],
                ],
            ],
        ];
    }

    protected static function indexSettings(): array
    {
        return [
            'index.knn'                     => true,
            'default_pipeline'              => 'embedding-ingest-pipeline',
            'index.search.default_pipeline' => 'hybrid-search-pipeline',
        ];
    }

    public static function synonymFields(): array
    {
        return ['name', 'short_description', 'description'];
    }
}
