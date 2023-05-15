<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Jobs\IndexJob;

abstract class ElasticsearchIndexCommand extends InteractsWithElasticsearchCommand
{
    protected bool $useJob = true;

    /**
     * Index all items in all stores.
     *
     * @param string $indexName
     * @param (callable(Store): iterable)|iterable $items
     * @param (callable(object): array)|array $values
     * @param (callable(object): int)|string $id
     */
    public function indexAllStores($indexName, $items, $values, $id = 'id')
    {
        $stores = Rapidez::getStores();
        foreach ($stores as $store) {
            $this->indexStore($store, $indexName, $items, $values, $id);
        }
    }

    /**
     * Index all items in a specific stores.
     *
     * @param Store $store
     * @param string $indexName
     * @param (callable(Store): iterable)|iterable $items
     * @param (callable(object): array)|array $values
     * @param (callable(object): int)|string $id
     */
    public function indexStore($store, $indexName, $items, $values, $id = 'id')
    {
        $this->line('Indexing `'.$indexName.'` for store: '.$store->name);

        try {
            [$alias, $index] = $this->prepareIndexer($store, $indexName);
            $this->indexItems($index, $items, $values, $id);
            $this->switchAlias($alias, $index);
        } catch (Exception $e) {
            $this->deleteIndex($index);

            throw $e;
        }
    }

    /**
     * Index a chunk of items
     *
     * @param string $index
     * @param (callable(Store): iterable)|iterable $items
     * @param (callable(object): array)|array $values
     * @param (callable(object): int)|string $id
     */
    public function indexItems($index, $items, $values, $id = 'id')
    {
        $currentData = value($items, config()->get('rapidez.store_code'));
        foreach($currentData as $item) {
            $this->indexItem($index, $item, $values, $id);
        }
    }

    /**
     * Index a single item
     *
     * @param string $index
     * @param object $item
     * @param (callable(object): array)|array $values
     * @param (callable(object): int)|string $id
     */
    public function indexItem($index, $item, $values, $id = 'id')
    {
        $currentValues = is_callable($values)
            ? $values($item)
            : Arr::only((array)$item, $values);

        $currentId = is_callable($id)
            ? $id($item)
            : $item[$id];

        if($this->useJob) {
            IndexJob::dispatch($index, $currentId, $currentValues);
        } else {
            $this->elasticsearch->index([
                'index' => $index,
                'id'    => $currentId,
                'body'  => $currentValues,
            ]);
        }
    }

    public function createAlias($store, $indexName): array
    {
        $alias = config('rapidez.es_prefix').'_'.$indexName.'_'.$store->store_id;

        return [$alias, $alias.'_'.Carbon::now()->format('YmdHis')];
    }

    public function prepareIndexer($store, $indexName, array $mapping = [], array $settings = []): array
    {
        Rapidez::setStore($store);
        [$alias, $index] = $this->createAlias($store, $indexName);
        $this->createIndex($index, $mapping, $settings);

        return [$alias, $index];
    }
}
