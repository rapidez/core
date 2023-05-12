<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Illuminate\Console\Command;
use Rapidez\Core\Models\Store;

abstract class InteractsWithElasticsearchCommand extends Command
{
    protected Elasticsearch $elasticsearch;

    public function __construct(Elasticsearch $elasticsearch)
    {
        parent::__construct();

        $this->elasticsearch = $elasticsearch;
    }

    public function createIndex(string $index, array $mapping = [], array $settings = []): void
    {
        $this->elasticsearch->indices()->create([
            'index' => $index,
            'body'  => array_filter([
                'mappings' => $mapping,
                'settings' => $settings,
            ]),
        ]);
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

    public function deleteIndex(string $index): void
    {
        $this->elasticsearch->indices()->delete(['index' => $index]);
    }

    public function getStores()
    {
        $storeModel = config('rapidez.models.store');
        return $this->argument('store')
            ? $storeModel::where('store_id', $this->argument('store'))->get()
            : $storeModel::all();
    }

    public function setStore(Store $store): void
    {
        config()->set('rapidez.store', $store->store_id);
        config()->set('rapidez.website', $store->website_id);
        $code = config('rapidez.models.store')::getCachedWhere(function ($store) {
            return $store['store_id'] == config('rapidez.store');
        })['code'];
        config()->set('rapidez.store_code', $code);
    }
}
