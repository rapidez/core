<?php

namespace Rapidez\Core\Commands;

use Elasticsearch\Common\Exceptions\UnexpectedValueException;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Support\Arrayable;
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
    public int $chunkSize = 0;

    public array $mapping = [];
    public array $settings = [];
    public array $synonymsFor = [];
    public array $extraSynonyms = [];

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
        if (is_array($stores)) {
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
        $this->mapping = array_merge_recursive($this->mapping, $mapping);

        return $this;
    }

    public function mapping(string $path, mixed $mapping, bool $overwrite = false): static
    {
        data_set($this->mapping, $path, $mapping, $overwrite);

        return $this;
    }

    public function useSettings(array $settings): static
    {
        $this->settings = array_merge_recursive($this->settings, $settings);

        return $this;
    }

    public function setting(string $path, mixed $setting, bool $overwrite = false): static
    {
        data_set($this->settings, $path, $setting, $overwrite);

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

    public function withSynonymsFor(array $for)
    {
        if (count($for)) {
            $this->synonymsFor = array_merge($this->synonymsFor, $for);
        }

        return $this;
    }

    public function withExtraSynonyms(array $synonyms)
    {
        if (count($synonyms)) {
            $this->extraSynonyms = array_merge($this->extraSynonyms, $synonyms);
        }

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
        $this->prepareIndexerWithStore($store, $indexName);
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

            throw new UnexpectedValueException('Items cannot be null.');
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
        if ($this->chunkSize < 1) {
            $this->indexPartial($items, $id);

            return;
        }

        $arrItems = $items instanceof Arrayable ? $items->toArray() : (array) $items;
        foreach (array_chunk($arrItems, $this->chunkSize) as $chunk) {
            $this->indexPartial($chunk, $id);
        }
    }

    public function indexPartialQuery(Builder $items, callable|string $id): void
    {
        if ($this->chunkSize < 1) {
            $this->indexPartial($items->get(), $id);

            return;
        }

        $items->chunk($this->chunkSize, function ($chunk) use ($id) {
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

    public function prepareIndexerWithStore(Store|array $store, string $indexName): void
    {
        Rapidez::setStore($store);
        $this->prepareSynonyms();
        $this->indexer->prepare(config('rapidez.es_prefix') . '_' . $indexName . '_' . $store['store_id'], $this->mapping, $this->settings);
    }

    public function prepareSynonyms(): void
    {
        if (count($this->synonymsFor)) {
            $synonyms = config('rapidez.models.search_synonym')::whereIn('store_id', [0, config('rapidez.store')])
                ->get()
                ->map(fn ($synonym) => $synonym->synonyms)
                ->toArray();

            $synonyms = array_merge($synonyms, $this->extraSynonyms);

            $this->setting('index.analysis.filter.synonym', ['type' => 'synonym', 'synonyms' => $synonyms]);
            $this->setting('index.analysis.analyzer.synonym', ['tokenizer' => 'whitespace', 'filter' => ['synonym']]);

            foreach ($this->synonymsFor as $property) {
                $this->mapping('properties.' . $property . '.type', 'text');
                $this->mapping('properties.' . $property . '.analyzer', 'synonym');
            }
        }
    }

    public function dataFrom(mixed $data)
    {
        return value($data, config()->get('rapidez.store_code'));
    }
}
