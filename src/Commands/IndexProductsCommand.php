<?php

namespace Rapidez\Core\Commands;

use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Jobs\IndexProductJob;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\Store;
use Rapidez\Core\Scopes\WithProductSwatchesScope;
use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use TorMorten\Eventy\Facades\Eventy;

class IndexProductsCommand extends Command
{
    protected $signature = 'rapidez:index {store? : Store ID from Magento} {--fresh : Recreate the indexes}';

    protected $description = 'Index the products in Elasticsearch';

    protected int $chunkSize = 500;

    protected string $index;
    protected string $indexToDelete;

    protected Elasticsearch $elasticsearch;

    public function __construct(Elasticsearch $elasticsearch)
    {
        parent::__construct();

        $this->elasticsearch = $elasticsearch;
    }

    public function handle()
    {
        $stores = $this->argument('store') ? Store::where('store_id', $this->argument('store'))->get() : Store::all();

        foreach ($stores as $store) {
            $this->line('Store: '.$store->name);
            config()->set('rapidez.store', $store->store_id);
            $this->index = config('rapidez.es_prefix') . '_products_v1_' . $store->store_id;

            $this->createIndexIfNeeded($this->option('fresh'));

            $flat = (new Product)->getTable();
            $productQuery = Product::where($flat.'.visibility', 4)->selectOnlyIndexable();

            $scopes = Eventy::filter('index.product.scopes') ?: [];
            foreach ($scopes as $scope) {
                $productQuery->withGlobalScope($scope, new $scope);
            }

            $bar = $this->output->createProgressBar($productQuery->count());
            $bar->start();

            $productQuery->chunk($this->chunkSize, function ($products) use ($store, $bar) {
                foreach ($products as $product) {
                    $data = array_merge(['store' => $store->store_id], $product->toArray());
                    foreach ($product->super_attributes ?: [] as $superAttribute) {
                        $data[$superAttribute->code] = array_keys((array)$product->{$superAttribute->code});
                    }
                    $data = Eventy::filter('index.product.data', $data, $product);
                    IndexProductJob::dispatch($data, $this->index);
                }

                $bar->advance($this->chunkSize);
            });

            $this->attachAlias($store->store_id);
            if ($this->elasticsearch->indices()->exists(['index' => $this->indexToDelete])) {
                $this->deleteIndex($this->indexToDelete);
            }

            $bar->finish();
            $this->line('');
        }
        $this->info('Done!');
    }

    public function createIndexIfNeeded($recreate = false): void
    {
        if ($recreate) {
            $this->indexToDelete = config('rapidez.es_prefix') . '_products_*';
            $this->deleteIndex();
        }

        if (!$this->elasticsearch->indices()->exists(['index' => $this->index])) {
            $this->createIndex($this->index);
            $this->indexToDelete = str_replace('v1', 'v2', $this->index);
        } else {
            $this->indexToDelete = $this->index;
            $this->index = str_replace('v1', 'v2', $this->index);
            $this->createIndex($this->index);
        }
    }

    public function createIndex(): void
    {
        try {
        $this->elasticsearch->indices()->create([
            'index' => $this->index,
            'body' => [
                'mappings' => [
                    'properties' => [
                        'price' => [
                            'type' => 'double',
                        ],
                        'children' => [
                            'type' => 'flattened',
                        ]
                    ]
                ]
            ]
        ]);
        } catch(Missing404Exception $e) {
            $this->line($e->error);
        }
    }

    public function deleteIndex(): void
    {
        try {
            $this->elasticsearch->indices()->delete(['index' => $this->indexToDelete]);
        } catch(Missing404Exception $e) {
            $this->line($e->error);        }
    }

    public function attachAlias(): void
    {
        $params['body'] = [
            'actions' => [
                [
                    'add' => [
                        'index' => $this->index,
                        'alias' => config('rapidez.es_prefix').'_products_'.config('rapidez.store')
                    ],
                ],
            ]
        ];
        try {
            $this->elasticsearch->indices()->updateAliases($params);
        } catch(Missing404Exception $e) {
            $this->line($e->error);        }
    }
}
