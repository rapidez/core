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

            $this->createIndexIfNeeded(config('rapidez.es_prefix') . '_products_' . $store->store_id, $this->option('fresh'));

            $productQuery = Product::where('visibility', 4)->selectOnlyIndexable();

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
                    IndexProductJob::dispatch($data);
                }

                $bar->advance($this->chunkSize);
            });

            $bar->finish();
            $this->line('');
        }
        $this->info('Done!');
    }

    public function createIndexIfNeeded(string $index, $recreate = false): void
    {
        if ($recreate) {
            try {
                $this->elasticsearch->indices()->delete(['index' => $index]);
            } catch (Missing404Exception $e) {
                //
            }
        }

        try {
            $this->elasticsearch->cat()->indices(['index' => $index]);
        } catch (Missing404Exception $e) {
            $this->elasticsearch->indices()->create([
                'index' => $index,
                'body'  => [
                    'mappings' => [
                        'properties' => [
                            'price' => [
                                'type' => 'double',
                            ]
                        ]
                    ]
                ]
            ]);
        }
    }
}
