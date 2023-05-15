<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Rapidez\Core\Facades\Rapidez;

abstract class ElasticsearchIndexCommand extends InteractsWithElasticsearchCommand
{
    /**
     * Index all items in all stores.
     *
     * @param string $indexName
     * @param (callable(object): array)|array $data
     * @param (callable(object): array)|array $values
     */
    public function indexAllStores($indexName, $data, $values)
    {
        $stores = Rapidez::getStores();
        foreach ($stores as $store) {
            $this->indexStore($store, $indexName, $data, $values);
        }
    }

    /**
     * Index all items in a specific stores.
     *
     * @param object $store
     * @param string $indexName
     * @param (callable(object): array)|array $data
     * @param (callable(object): array)|array $values
     */
    public function indexStore($store, $indexName, $data, $values)
    {
        $this->line('Indexing `'.$indexName.'` for store: '.$store->name);
        try {
            [$alias, $index] = $this->prepareIndexer($store, $indexName);
            $currentData = value($data, config()->get('rapidez.store_code'));
            foreach ($currentData as $item) {
                $this->indexItem($index, $item, $item->id, $values);
            }
            $this->switchAlias($alias, $index);
        } catch (Exception $e) {
            $this->deleteIndex($index);
            throw $e;
        }
    }

    public function createAlias($store, $indexName): array
    {
        $alias = config('rapidez.es_prefix').'_'.$indexName.'_'.$store->store_id;
        return [$alias, $alias.'_'.Carbon::now()->format('YmdHis')];
    }

    public function prepareIndexer($store, $indexName): array
    {
        Rapidez::setStore($store);
        [$alias, $index] = $this->createAlias($store, $indexName);
        $this->createIndex($index);
        return [$alias, $index];
    }

    public function indexItem($index, $item, $id, $values)
    {
        $currentValues = is_callable($values)
            ? $values($item)
            : Arr::only($item, $values);

        $this->elasticsearch->index([
            'index' => $index,
            'id'    => $id,
            'body'  => $currentValues,
        ]);
    }
}
