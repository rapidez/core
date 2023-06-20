<?php

namespace Rapidez\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Store;

abstract class ElasticsearchIndexCommand extends Command
{
    public ElasticsearchIndexer $indexer;

    public function __construct(ElasticsearchIndexer $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
    }

    public function indexAllStores(string $indexName, callable|iterable $items, callable|array $mapping, callable|string $id = 'id'): void
    {
        $this->indexStores(Rapidez::getStores(), $indexName, $items, $mapping, $id);
    }

    public function indexStores(array $stores, string $indexName, callable|iterable $items, callable|array $mapping, callable|string $id = 'id'): void
    {
        foreach ($stores as $store) {
            $this->indexStore($store, $indexName, $items, $mapping, $id);
        }
    }

    public function indexStore(Store|array $store, string $indexName, callable|iterable $items, callable|array $mapping, callable|string $id = 'id'): void
    {
        $storeName = $store['name'] ?? $store['code'] ?? reset($store);
        $this->line('Indexing `' . $indexName . '` for store ' . $storeName);

        try {
            $this->prepareIndexerWithStore($store, $indexName);
            $this->indexer->index($this->dataFrom($items), $mapping, $id);
            $this->indexer->finish();
        } catch (Exception $e) {
            $this->indexer->abort();

            throw $e;
        }
    }

    public function prepareIndexerWithStore(Store|array $store, string $indexName, array $mapping = [], array $settings = []): void
    {
        Rapidez::setStore($store);
        $this->indexer->prepare(config('rapidez.es_prefix') . '_' . $indexName . '_' . $store['store_id'], $mapping, $settings);
    }

    public function dataFrom(callable|iterable $items)
    {
        return value($items, config()->get('rapidez.store_code'));
    }
}
