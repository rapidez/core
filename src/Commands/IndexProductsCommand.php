<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Rapidez\Core\Jobs\IndexProductJob;
use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\Store;
use Rapidez\Core\Scopes\WithProductSwatchesScope;
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
        $stores = $this->argument('store') ? Store::where('store_id', $this->argument('store'))->get() : Store::all();

        foreach ($stores as $store) {
            $this->line('Store: '.$store->name);
            config()->set('rapidez.store', $store->store_id);

            $alias = config('rapidez.es_prefix') . '_products_' . $store->store_id;
            $index = $alias . '_' . Carbon::now()->format('YmdHis');
            $this->createIndex($index);

            $flat = (new Product)->getTable();
            $productQuery = Product::where($flat.'.visibility', 4)->selectOnlyIndexable();

            $scopes = Eventy::filter('index.product.scopes') ?: [];
            foreach ($scopes as $scope) {
                $productQuery->withGlobalScope($scope, new $scope);
            }

            $bar = $this->output->createProgressBar($productQuery->count());
            $bar->start();

            $productQuery->chunk($this->chunkSize, function ($products) use ($store, $bar, $index) {
                foreach ($products as $product) {
                    $data = array_merge(['store' => $store->store_id], $product->toArray());
                    foreach ($product->super_attributes ?: [] as $superAttribute) {
                        $data[$superAttribute->code] = array_keys((array)$product->{$superAttribute->code});
                    }
                    $data = Eventy::filter('index.product.data', $data, $product);
                    IndexProductJob::dispatch($index, $data);
                }

                $bar->advance($this->chunkSize);
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
            'name' => $alias,
        ]);

        $aliases = $this->elasticsearch->indices()->getAlias(['name' => $alias]);
        foreach ($aliases as $indexLinkedToAlias => $aliasData) {
            if ($indexLinkedToAlias != $index) {
                $this->elasticsearch->indices()->deleteAlias([
                    'index' => $indexLinkedToAlias,
                    'name' => $alias,
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
                    ]
                ])
            ]
        ]);
    }
}
