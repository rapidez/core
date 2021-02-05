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
    protected $signature = 'rapidez:index {--fresh : Recreate the indexes}';

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
        foreach (Store::all() as $store) {
            $this->line('Store: '.$store->name);
            config()->set('rapidez.store', $store->store_id);
            $this->index = config('rapidez.es_prefix') . '_products_' . $store->store_id;

            $this->createIndexIfNeeded($this->index, $this->option('fresh'));

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

            if ($this->elasticsearch->indices()->exists(['index' => $this->indexToDelete])) {
                try {
                    $this->deleteIndex($this->indexToDelete);
                } catch (\Exception $e) {
                    //
                }
            }

            try {
                $this->attachAlias($this->index);
            } catch (\Exception $e) {

            }

            $bar->finish();
            $this->line('');
        }
        $this->info('Done!');
    }

    public function createIndexIfNeeded(string $index, $recreate = false): void
    {
        if ($recreate) {
            try {
                $this->elasticsearch->indices()->delete(['index' => '*']);
            } catch (Missing404Exception $e) {
                //
            }
        }

        if (!$this->elasticsearch->indices()->exists(['index' => $index])) {
            $this->createIndex($index);
            $this->indexToDelete = str_replace(1, 2, $index);
            $this->index = $index;
        } else {
            $index++;
            $this->createIndex($index);
            $this->indexToDelete = str_replace(2, 1, $index);
            $this->index = $index;
        }
    }

    public function createIndex(string $index): void
    {
        $this->elasticsearch->indices()->create([
            'index' => $index,
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
    }

    public function deleteIndex(string $oldIndex): void
    {
        try {
            $this->elasticsearch->indices()->delete(['index' => $oldIndex]);
        } catch (Missing404Exception $e) {

        }
    }

    public function attachAlias(string $index): void
    {
        $params['body'] = [
            'actions' => [
                [
                    'add' => [
                        'index' => $index,
                        'alias' => 'rapidez_products'
                    ],
                ],
            ]
        ];

        $this->elasticsearch->indices()->updateAliases($params);
    }
}
