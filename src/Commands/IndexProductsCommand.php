<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Illuminate\Console\Command;
use Rapidez\Core\Jobs\IndexProductJob;
use Rapidez\Core\Models\Category;
use Rapidez\Core\RapidezFacade;
use TorMorten\Eventy\Facades\Eventy;

class IndexProductsCommand extends Command
{
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
            $this->createIndex($index);

            $flat = (new $productModel())->getTable();
            $productQuery = $productModel::where($flat.'.visibility', 4)
                ->selectOnlyIndexable()
                ->withEventyGlobalScopes('index.product.scopes');

            $bar = $this->output->createProgressBar($productQuery->getQuery()->getCountForPagination());
            $bar->start();

            $categories = Category::query()
                ->where(fn ($q) => $q->whereNull('display_mode')->orWhere('display_mode', '<>', 'PAGE'))
                ->where('entity_id', '<>', RapidezFacade::config('catalog/category/root_id', 2))
                ->pluck('name', 'entity_id');

            $productQuery->chunk($this->chunkSize, function ($products) use ($store, $bar, $index, $categories) {
                foreach ($products as $product) {
                    $data = array_merge(['store' => $store->store_id], $product->toArray());
                    foreach ($product->super_attributes ?: [] as $superAttribute) {
                        $data[$superAttribute->code] = array_keys((array) $product->{$superAttribute->code});
                    }

                    // TODO: Extract this to somewhere else?
                    $data['category_paths'] = explode(',', $data['category_paths']);
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
                    $data = Eventy::filter('index.product.data', $data, $product);
                    IndexProductJob::dispatch($index, $data);
                }

                $bar->advance($products->count());
            });

            $this->switchAlias($alias, $index);

            $bar->finish();
            $this->line('');
        }
        $this->info('Done!');
    }

    public function switchAlias(string $alias, string $index): void
    {
        $this->elasticsearch->indices()->putAlias([
            'index' => $index,
            'name'  => $alias,
        ]);

        $aliases = $this->elasticsearch->indices()->getAlias(['name' => $alias]);
        foreach ($aliases as $indexLinkedToAlias => $aliasData) {
            if ($indexLinkedToAlias != $index) {
                $this->elasticsearch->indices()->deleteAlias([
                    'index' => $indexLinkedToAlias,
                    'name'  => $alias,
                ]);
                $this->elasticsearch->indices()->delete(['index' => $indexLinkedToAlias]);
            }
        }
    }

    public function createIndex(string $index): void
    {
        $this->elasticsearch->indices()->create([
            'index' => $index,
            'body'  => [
                'mappings' => Eventy::filter('index.product.mapping', [
                    'properties' => [
                        'price' => [
                            'type' => 'double',
                        ],
                        'children' => [
                            'type' => 'flattened',
                        ],
                    ],
                ]),
            ],
        ]);
    }
}
