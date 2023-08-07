<?php

namespace Rapidez\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Store;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class ElasticsearchIndexCommand extends Command
{
    public ElasticsearchIndexer $indexer;

    public array $stores;
    public string $indexName;
    public mixed $dataFilter = null;
    public bool $progressBar = true;
    public array $mapping = [];
    public array $settings = [];
    public int $chunkSize = 0;

    public Builder $query;
    public ProgressBar $bar;

    public function __construct(ElasticsearchIndexer $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
        $this->stores = Rapidez::getStores();
    }

    public function onlyStores(callable|int|string|array|null $stores): static
    {
        if(is_array($stores)) {
            $this->stores = $stores;
        } else {
            $this->stores = Rapidez::getStores($stores);
        }

        return $this;
    }

    public function onlyStore(Store|array $store): static
    {
        $this->stores = [$store];

        return $this;
    }

    public function withFilter(callable|array|null $dataFilter): static
    {
        $this->dataFilter = $dataFilter;

        return $this;
    }

    public function useMapping(array $mapping): static
    {
        $this->mapping = $mapping;

        return $this;
    }

    public function useSettings(array $settings): static
    {
        $this->settings = $settings;

        return $this;
    }

    public function chunk(int $chunkSize): static
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }

    public function withoutProgressBar()
    {
        $this->progressBar = false;

        return $this;
    }

    public function index(string $indexName, callable|iterable|Builder $items, callable|string $id = 'entity_id'): void
    {
        foreach ($this->stores as $store) {
            $this->indexStore(
                store: $store,
                indexName: $indexName,
                items: $items,
                id: $id,
            );
        }
    }

    public function initIndex(Store|array $store, string $indexName)
    {
        $storeName = $store['name'] ?? $store['code'] ?? reset($store);
        $this->line('Indexing `' . $indexName . '` for store ' . $storeName);
        $this->prepareIndexerWithStore($store, $indexName, $this->mapping, $this->settings);
    }

    public function beforeIndex(int $totalCount)
    {
        if ($this->progressBar) {
            $this->bar = $this->output->createProgressBar($totalCount);
            $this->bar->start();
        }
    }

    public function afterIndex()
    {
        $this->indexer->finish();
        if ($this->progressBar) {
            $this->bar->finish();
            $this->line('');
        }
    }

    public function indexStore(Store|array $store, string $indexName, callable|iterable|Builder $items, callable|string $id): void
    {
        $this->initIndex($store, $indexName);
        $fullItems = $this->dataFrom($items);

        if (is_null($fullItems)) {
            $this->indexer->abort();

            throw new Exception('Items cannot be null.');
        }

        $count = is_iterable($fullItems) ? count($fullItems) : $fullItems->getQuery()->getCountForPagination();

        $this->beforeIndex($count);
        $this->tryIndexItems($fullItems, $id);
        $this->afterIndex();
    }

    public function tryIndexItems(iterable|Builder $items, callable|string $id): void
    {
        try {
            if (is_iterable($items)) {
                $this->indexPartialIterable($items, $id);
            } else {
                $this->indexPartialQuery($items, $id);
            }
        } catch (Exception $e) {
            $this->indexer->abort();

            throw $e;
        }
    }

    public function indexPartialIterable(iterable $items, callable|string $id): void
    {
        if($this->chunkSize < 1) {
            $this->indexPartial($items, $id);
            return;
        }

        foreach (array_chunk((array)$items, $this->chunkSize) as $chunk) {
            $this->indexPartial($chunk, $id);
        }
    }

    public function indexPartialQuery(Builder $items, callable|string $id): void
    {
        if($this->chunkSize < 1) {
            $this->indexPartial($items->get(), $id);
            return;
        }

        $items->chunk($this->chunkSize, function($chunk) use ($id) {
            $this->indexPartial($chunk, $id);
        });
    }

    public function indexPartial(iterable $items, callable|string $id): void
    {
        $this->indexer->index($items, $this->dataFilter, $id);
        if ($this->progressBar) {
            $this->bar->advance(count($items));
        }
    }

    public function prepareIndexerWithStore(Store|array $store, string $indexName, array|null $mapping, array|null $settings): void
    {
        Rapidez::setStore($store);
        $this->indexer->prepare(config('rapidez.es_prefix') . '_' . $indexName . '_' . $store['store_id'], $mapping ?? $this->mapping, $settings ?? $this->settings);
    }

    public function dataFrom(mixed $data)
    {
        return value($data, config()->get('rapidez.store_code'));
    }
}
