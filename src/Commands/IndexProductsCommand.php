<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Jobs\IndexProductJob;
use Rapidez\Core\Models\Category;
use TorMorten\Eventy\Facades\Eventy;
use Rapidez\Core\Commands\Traits\SwapIndexes;

class IndexProductsCommand extends Command
{
    use SwapIndexes;

    protected $signature = 'rapidez:index {store? : Store ID from Magento}';

    protected $description = 'Index the products in Elasticsearch';

    protected int $chunkSize = 500;

    protected Elasticsearch $elasticsearch;

    public function __construct(Elasticsearch $elasticsearch)
    {
        parent::__construct();

        $this->elasticsearch = $elasticsearch;
    }

    public function handle()
    {
        $this->call('cache:clear');

        $productModel = config('rapidez.models.product');
        $storeModel = config('rapidez.models.store');
        $stores = $this->argument('store') ? $storeModel::where('store_id', $this->argument('store'))->get() : $storeModel::all();

        foreach ($stores as $store) {
            $this->line('Store: '.$store->name);
            config()->set('rapidez.store', $store->store_id);

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
            ]));

            try {
                $flat = (new $productModel())->getTable();
                $productQuery = $productModel::where($flat.'.visibility', 4)
                    ->selectOnlyIndexable()
                    ->withEventyGlobalScopes('index.product.scopes');

                $bar = $this->output->createProgressBar($productQuery->getQuery()->getCountForPagination());
                $bar->start();

                $categories = Category::query()
                    ->where('entity_id', '<>', Rapidez::config('catalog/category/root_id', 2))
                    ->pluck('name', 'entity_id');

                $productQuery->chunk($this->chunkSize, function ($products) use ($store, $bar, $index, $categories) {
                    foreach ($products as $product) {
                        $data = array_merge(['store' => $store->store_id], $product->toArray());
                        foreach ($product->super_attributes ?: [] as $superAttribute) {
                            $data[$superAttribute->code] = array_keys((array) $product->{$superAttribute->code});
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
