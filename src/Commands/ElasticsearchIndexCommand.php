<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Rapidez\Core\Commands\InteractsWithElasticsearchCommand;

abstract class ElasticsearchIndexCommand extends InteractsWithElasticsearchCommand
{
    /**
     * Index all items in all stores
     *
     * @param  string $indexName
     * @param  (callable(object): array)|array $data
     * @param  (callable(object): array)|array $values
     */
    public function indexAll($indexName, $data, $values)
    {
        $stores = $this->getStores();
        foreach ($stores as $store) {
            $this->indexStore($store, $indexName, $data, $values);
        }
    }

    /**
     * Index all items in a specific stores
     *
     * @param  object $store
     * @param  string $indexName
     * @param  (callable(object): array)|array $data
     * @param  (callable(object): array)|array $values
     */
    public function indexStore($store, $indexName, $data, $values)
    {
        $this->line('Indexing `'.$indexName.'` for store: '.$store->name);
        $this->setStore($store);
        $alias = $this->createAlias($store, $indexName);
        $index = $alias.'_'.Carbon::now()->format('YmdHis');

        $this->createIndex($index);

        $currentData = is_callable($data)
            ? $data(config()->get('rapidez.store_code'))
            : $data;

        foreach ($currentData as $item) {
            $this->indexItem($item, $values, $index);
        }

        $this->switchAlias($alias, $index);
    }

    /**
     * Creates an alias for the current index
     *
     * @param  object $store
     * @param  string $indexName
     */
    public function createAlias($store, $indexName): string
    {
        return config('rapidez.es_prefix').'_'.$indexName.'_'.$store->store_id;
    }

    public function indexItem($item, $values, $index)
    {
        $currentValues = is_callable($values)
            ? $values($item)
            : collect($item)->only($values);

        $this->elasticsearch->index([
            'index' => $index,
            'id' => $item->id,
            'body' => $currentValues,
        ]);
    }
}
