<?php

namespace Rapidez\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Store;

abstract class ElasticsearchIndexCommand extends Command
{
    public ElasticsearchIndexer $indexer;

    public array $mapping = [];
    public array $settings = [];
    public array $synonymsFor = [];

    public function __construct(ElasticsearchIndexer $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
    }

    public function indexAllStores(string $indexName, callable|iterable $items, callable|array|null $dataFilter, callable|string $id = 'entity_id'): void
    {
        $this->indexStores(Rapidez::getStores(), $indexName, $items, $dataFilter, $id);
    }

    public function indexStores(array $stores, string $indexName, callable|iterable $items, callable|array|null $dataFilter, callable|string $id = 'entity_id'): void
    {
        foreach ($stores as $store) {
            $this->indexStore($store, $indexName, $items, $dataFilter, $id);
        }
    }

    public function indexStore(Store|array $store, string $indexName, callable|iterable $items, callable|array $dataFilter, callable|string $id = 'entity_id'): void
    {
        $storeName = $store['name'] ?? $store['code'] ?? reset($store);
        $this->line('Indexing `' . $indexName . '` for store ' . $storeName);

        try {
            $this->prepareIndexerWithStore($store, $indexName, $this->mapping, $this->settings, $this->synonymsFor);
            $this->indexer->index($this->dataFrom($items), $dataFilter, $id);
            $this->indexer->finish();
        } catch (Exception $e) {
            $this->indexer->abort();

            throw $e;
        }
    }

    public function prepareIndexerWithStore(Store|array $store, string $indexName, array $mapping = [], array $settings = [], array $synonymsFor = []): void
    {
        Rapidez::setStore($store);
        $this->indexer->prepare(config('rapidez.es_prefix') . '_' . $indexName . '_' . $store['store_id'], $mapping, $settings, $synonymsFor);
    }

    public function dataFrom(callable|iterable $items)
    {
        return value($items, config()->get('rapidez.store_code'));
    }
}
