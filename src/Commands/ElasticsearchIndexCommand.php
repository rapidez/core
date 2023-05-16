<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Jobs\IndexJob;
use Rapidez\Core\Models\Store;

abstract class ElasticsearchIndexCommand extends InteractsWithElasticsearchCommand
{
    public string $alias;
    public string $index;

    public function indexAllStores(string $indexName, callable|iterable $items, callable|array $mapping, callable|string $id = 'id')
    {
        $stores = Rapidez::getStores();
        foreach ($stores as $store) {
            $this->indexStore($store, $indexName, $items, $mapping, $id);
        }
    }

    public function indexStore(Store $store, string $indexName, callable|iterable $items, callable|array $mapping, callable|string $id = 'id')
    {
        $this->line('Indexing `'.$indexName.'` for store: '.$store->name);

        try {
            $this->prepareIndex($store, $indexName);
            $this->indexItems($items, $mapping, $id);
            $this->finishIndex();
        } catch (Exception $e) {
            $this->abortIndex();

            throw $e;
        }
    }

    public function indexItems(callable|iterable $items, callable|array $mapping, callable|string $id = 'id')
    {
        $currentData = value($items, config()->get('rapidez.store_code'));
        foreach ($currentData as $item) {
            $this->indexItem($item, $mapping, $id);
        }
    }

    public function indexItem(object $item, callable|array $mapping, callable|string $id = 'id')
    {
        $currentValues = is_callable($mapping)
            ? $mapping($item)
            : Arr::only((array) $item, $mapping);

        $currentId = is_callable($id)
            ? $id($item)
            : $item[$id];

        IndexJob::dispatch($this->index, $currentId, $currentValues);
    }

    public function finishIndex()
    {
        $this->switchAlias($this->alias, $this->index);
    }

    public function abortIndex()
    {
        $this->deleteIndex($this->index);
    }

    public function createAlias($store, $indexName)
    {
        $this->alias = config('rapidez.es_prefix').'_'.$indexName.'_'.$store->store_id;
        $this->index = $this->alias.'_'.Carbon::now()->format('YmdHis');
    }

    public function prepareIndex($store, $indexName, array $mapping = [], array $settings = [])
    {
        Rapidez::setStore($store);
        $this->createAlias($store, $indexName);
        $this->createIndex($this->index, $mapping, $settings);
    }
}
