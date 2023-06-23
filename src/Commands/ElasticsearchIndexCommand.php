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
        $stores = Rapidez::getStores();
        foreach ($stores as $store) {
            $this->indexStore($store, $indexName, $items, $mapping, $id);
        }
    }

    public function indexStore(Store|array $store, string $indexName, callable|iterable $items, callable|array $mapping, callable|string $id = 'id'): void
    {
        $this->line('Indexing `' . $indexName . '` for store ' . $store['name']);

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
