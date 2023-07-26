<?php

namespace Rapidez\Core\Commands;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Events\IndexAfterEvent;
use Rapidez\Core\Events\IndexBeforeEvent;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Category;
use TorMorten\Eventy\Facades\Eventy;

class IndexProductsCommand extends ElasticsearchIndexCommand
{
    protected $signature = 'rapidez:index {store? : Store ID from Magento}';

    protected $description = 'Index the products in Elasticsearch';

    public function handle()
    {
        $this->call('cache:clear');

        IndexBeforeEvent::dispatch($this);

        $productModel = config('rapidez.models.product');

        $mapping = Eventy::filter('index.product.mapping', [
            'properties' => [
                'price' => [
                    'type' => 'double',
                ],
                'children' => [
                    'type' => 'flattened',
                ],
                'grouped' => [
                    'type' => 'flattened',
                ],
            ],
        ]);

        $settings = Eventy::filter('index.product.settings', []);

        $this->onlyStores($this->argument('store'))
            ->useMapping($mapping)
            ->useSettings($settings)
            ->withFilter($this->productFilter(...))
            ->chunk(500)
            ->index(
                indexName: 'products',
                items: fn() => $productModel::selectOnlyIndexable()->withEventyGlobalScopes('index.product.scopes'),
            );

        IndexAfterEvent::dispatch($this);
        $this->info('Done!');
    }

    public function productFilter($product)
    {
        if (!$product->in_stock && !(bool) Rapidez::config('cataloginventory/options/show_out_of_stock', 0)) {
            return;
        }

        $data = array_merge(['store' => config('rapidez.store')], $product->toArray());

        $categories = Cache::driver('array')->rememberForever(config('rapidez.store'), function() {
            return Category::query()
                ->where('catalog_category_flat_store_' . config('rapidez.store') . '.entity_id', '<>', Rapidez::config('catalog/category/root_id', 2))
                ->pluck('name', 'entity_id');
        });

        foreach ($product->super_attributes ?: [] as $superAttribute) {
            $data['super_' . $superAttribute->code] = $superAttribute->text_swatch || $superAttribute->visual_swatch
                ? array_keys((array) $product->{'super_' . $superAttribute->code})
                : Arr::pluck($product->{'super_' . $superAttribute->code} ?: [], 'label');
        }

        $data = $this->withCategories($data, $categories);

        return Eventy::filter('index.product.data', $data, $product);
    }

    public function withCategories(array $data, Collection $categories): array
    {
        foreach ($data['category_paths'] as $categoryPath) {
            $category = [];
            foreach (explode('/', $categoryPath) as $categoryId) {
                if (isset($categories[$categoryId])) {
                    $category[] = $categoryId . '::' . $categories[$categoryId];
                }
            }
            if (! empty($category)) {
                $data['categories'][] = implode(' /// ', $category);
            }
        }

        return $data;
    }
}
