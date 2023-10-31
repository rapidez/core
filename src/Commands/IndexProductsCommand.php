<?php

namespace Rapidez\Core\Commands;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Rapidez\Core\Events\IndexAfterEvent;
use Rapidez\Core\Events\IndexBeforeEvent;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Category;
use TorMorten\Eventy\Facades\Eventy;

class IndexProductsCommand extends ElasticsearchIndexCommand
{
    protected $signature = 'rapidez:index {store? : Store ID from Magento}';

    protected $description = 'Index the products in Elasticsearch';

    protected int $chunkSize = 500;

    public function handle()
    {
        $this->call('cache:clear');

        IndexBeforeEvent::dispatch($this);

        $productModel = config('rapidez.models.product');
        $stores = Rapidez::getStores($this->argument('store'));
        foreach ($stores as $store) {
            $this->line('Store: ' . $store['name']);
            $this->prepareIndexerWithStore($store, 'products', Eventy::filter('index.product.mapping', [
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
            ]), Eventy::filter('index.product.settings', []));

            try {
                $productQuery = $productModel::selectOnlyIndexable()
                    ->withEventyGlobalScopes('index.product.scopes')
                    ->withExists('options AS has_options');

                $bar = $this->output->createProgressBar($productQuery->getQuery()->getCountForPagination());
                $bar->start();

                $categories = Category::query()
                    ->where('catalog_category_flat_store_' . config('rapidez.store') . '.entity_id', '<>', Rapidez::config('catalog/category/root_id', 2))
                    ->pluck('name', 'entity_id');

                $showOutOfStock = (bool) Rapidez::config('cataloginventory/options/show_out_of_stock', 0);
                $indexVisibility = config('rapidez.indexer.index_visibility');

                $productQuery->chunk($this->chunkSize, function ($products) use ($store, $bar, $categories, $showOutOfStock, $indexVisibility) {
                    $this->indexer->index($products, function ($product) use ($store, $categories, $showOutOfStock, $indexVisibility) {
                        if (! in_array($product->visibility, $indexVisibility)) {
                            return;
                        }

                        if (! $showOutOfStock && ! $product->in_stock) {
                            return;
                        }

                        $data = array_merge(['store' => $store['store_id']], $product->toArray());

                        foreach ($product->super_attributes ?: [] as $superAttribute) {
                            $data['super_' . $superAttribute->code] = $superAttribute->text_swatch || $superAttribute->visual_swatch
                                ? array_keys((array) $product->{'super_' . $superAttribute->code})
                                : Arr::pluck($product->{'super_' . $superAttribute->code} ?: [], 'label');
                        }

                        $data = $this->withCategories($data, $categories);

                        return Eventy::filter('index.product.data', $data, $product);
                    });

                    $bar->advance($products->count());
                });

                $this->indexer->finish();
            } catch (Exception $e) {
                $this->indexer->abort();

                throw $e;
            }

            $bar->finish();
            $this->line('');
        }

        IndexAfterEvent::dispatch($this);
        $this->info('Done!');
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
