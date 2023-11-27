<?php

namespace Rapidez\Core\Commands;

use Elasticsearch\Common\Exceptions\UnexpectedValueException;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\LazyCollection;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Store;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class ElasticsearchIndexCommand extends Command
{
    public ElasticsearchIndexer $indexer;
    public ElasticsearchIndexParameters $params;
    public ProgressBar $bar;
    public LazyCollection $generator;

    public function __construct(ElasticsearchIndexer $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
    }

    public function index(callable|iterable|Builder|LazyCollection $items, ElasticsearchIndexParameters $params = null): void
    {
        if (! $params) {
            $this->params = new ElasticsearchIndexParameters('items');
        } else {
            $this->params = $params;
        }

        foreach ($this->params->stores as $store) {
            $this->indexStore(
                store: $store,
                indexName: $this->params->indexName,
                items: $items,
                id: $this->params->id,
            );
        }
    }

    public function initIndex(Store|array $store, string $indexName)
    {
        $storeName = $store['name'] ?? $store['code'] ?? reset($store);
        $this->line('Indexing `' . $indexName . '` for store ' . $storeName);
        $this->prepareIndexerWithStore($store, $indexName);
    }

    public function beforeIndex()
    {
        if ($this->params->progressBar) {
            $this->bar = $this->output->createProgressBar(count($this->generator));
            $this->bar->start();

            $this->generator = $this->generator->map(function ($value) {
                $this->bar->advance();

                return $value;
            });
        }
    }

    public function afterIndex()
    {
        $this->indexer->finish();

        if ($this->params->progressBar) {
            $this->bar->finish();
            $this->line('');
        }
    }

    public function indexStore(Store|array $store, string $indexName, callable|iterable|Builder|LazyCollection $items, callable|string $id): void
    {
        $this->initIndex($store, $indexName);
        $fullItems = $this->dataFrom($items);

        if (is_null($fullItems)) {
            $this->indexer->abort();

            throw new UnexpectedValueException('Items cannot be null.');
        }

        $this->generator = $this->generatorFrom($fullItems);

        $this->beforeIndex();
        $this->tryIndex($id);
        $this->afterIndex();
    }

    public function tryIndex(callable|string $id): void
    {
        try {
            $this->indexer->index($this->generator, $this->params->dataFilter, $id);
        } catch (Exception $e) {
            $this->indexer->abort();
            throw $e;
        }
    }

    public function generatorFrom(iterable|Builder|LazyCollection $items): LazyCollection
    {
        if ($items instanceof LazyCollection) {
            return $items;
        }

        if ($items instanceof Builder) {
            if (! $this->params->chunkSize) {
                return LazyCollection::wrap($items->get());
            }

            return $items->lazy($this->params->chunkSize);
        }

        return LazyCollection::wrap($items);
    }

    public function prepareIndexerWithStore(Store|array $store, string $indexName): void
    {
        Rapidez::setStore($store);
        $this->prepareSynonyms();
        $this->indexer->prepare(config('rapidez.es_prefix') . '_' . $indexName . '_' . $store['store_id'], $this->params->mapping, $this->params->settings);
    }

    public function prepareSynonyms(): void
    {
        if (count($this->params->synonymsFor)) {
            $synonyms = config('rapidez.models.search_synonym')::whereIn('store_id', [0, config('rapidez.store')])
                ->get()
                ->map(fn ($synonym) => $synonym->synonyms)
                ->toArray();

            $synonyms = array_merge($synonyms, $this->params->extraSynonyms);

            $this->params->addSetting('index.analysis.filter.synonym', ['type' => 'synonym', 'synonyms' => $synonyms]);
            $this->params->addSetting('index.analysis.analyzer.synonym', ['tokenizer' => 'whitespace', 'filter' => ['synonym']]);

            foreach ($this->params->synonymsFor as $property) {
                $this->params->addMapping('properties.' . $property . '.type', 'text');
                $this->params->addMapping('properties.' . $property . '.analyzer', 'synonym');
            }
        }
    }

    public function dataFrom(mixed $data)
    {
        return value($data, config()->get('rapidez.store_code'));
    }
}
