<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Exception;
use Illuminate\Console\Command;
use Rapidez\Core\Jobs\IndexProductJob;
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

            try {
                $flat = (new $productModel())->getTable();
                $productQuery = $productModel::where($flat.'.visibility', 4)
                    ->selectOnlyIndexable()
                    ->withEventyGlobalScopes('index.product.scopes');

                $bar = $this->output->createProgressBar($productQuery->getQuery()->getCountForPagination());
                $bar->start();

                $productQuery->chunk($this->chunkSize, function ($products) use ($store, $bar, $index) {
                    foreach ($products as $product) {
                        $data = array_merge(['store' => $store->store_id], $product->toArray());
                        foreach ($product->super_attributes ?: [] as $superAttribute) {
                            $data[$superAttribute->code] = array_keys((array) $product->{$superAttribute->code});
                        }
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
