<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Rapidez\Core\Events\IndexAfterEvent;
use Rapidez\Core\Events\IndexBeforeEvent;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Jobs\IndexProductJob;
use Rapidez\Core\Models\Category;
use TorMorten\Eventy\Facades\Eventy;

class IndexProductsCommand extends InteractsWithElasticsearchCommand
{
    protected $signature = 'rapidez:index {store? : Store ID from Magento}';

    protected $description = 'Index the products in Elasticsearch';

    protected int $chunkSize = 500;

    public function handle()
    {
        $this->call('cache:clear');

        IndexBeforeEvent::dispatch($this);

        $productModel = config('rapidez.models.product');
        $storeModel = config('rapidez.models.store');
        $stores = $this->argument('store') ? $storeModel::where('store_id', $this->argument('store'))->get() : $storeModel::all();

        foreach ($stores as $store) {
            $this->line('Store: '.$store->name);
            config()->set('rapidez.store', $store->store_id);
            config()->set('rapidez.website', $store->website_id);

            $alias = config('rapidez.es_prefix').'_products_'.$store->store_id;
            $index = $alias.'_'.Carbon::now()->format('YmdHis');
            $this->createIndex($index, Eventy::filter('index.product.mapping', [
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
                $flat = (new $productModel())->getTable();
                $productQuery = $productModel::selectOnlyIndexable()
                    ->withEventyGlobalScopes('index.product.scopes');

                $bar = $this->output->createProgressBar($productQuery->getQuery()->getCountForPagination());
                $bar->start();

                $categories = Category::query()
                    ->where('catalog_category_flat_store_'.config('rapidez.store').'.entity_id', '<>', Rapidez::config('catalog/category/root_id', 2))
                    ->pluck('name', 'entity_id');

                $showOutOfStock = (bool)Rapidez::config('cataloginventory/options/show_out_of_stock', 0);

                $productQuery->chunk($this->chunkSize, function ($products) use ($store, $bar, $index, $categories, $showOutOfStock) {
                    foreach ($products as $product) {
                        if (!$showOutOfStock && !$product->in_stock) {
                            continue;
                        }

                        $data = array_merge(['store' => $store->store_id], $product->toArray());
                        foreach ($product->super_attributes ?: [] as $superAttribute) {
                            $data['super_'.$superAttribute->code] = $superAttribute->text_swatch || $superAttribute->visual_swatch
                                ? array_keys((array) $product->{'super_'.$superAttribute->code})
                                : Arr::pluck($product->{'super_'.$superAttribute->code} ?: [], 'label');
                        }

                        $data = $this->withCategories($data, $categories);
                        $data = Eventy::filter('index.product.data', $data, $product);
                        IndexProductJob::dispatch($index, $data);
                    }

                    $bar->advance($products->count());
                });

                $this->switchAlias($alias, $index);
            } catch (Exception $e) {
                $this->elasticsearch->indices()->delete(['index' => $index]);

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
                    $category[] = $categoryId.'::'.$categories[$categoryId];
                }
            }
            if (!empty($category)) {
                $data['categories'][] = implode(' /// ', $category);
            }
        }

        return $data;
    }
}
