<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Rapidez\Core\Jobs\IndexJob;

class ElasticsearchIndexer
{
    public string $alias;
    public string $index;
    public bool $prepared = false;

    protected Elasticsearch $elasticsearch;

    public function __construct(Elasticsearch $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function deleteIndex(string $index): void
    {
        $this->elasticsearch->indices()->delete(['index' => $index]);
    }

    public function index(iterable|object $data, callable|array|null $dataFilter = null, callable|string $id): void
    {
        if (is_iterable($data)) {
            $this->indexItems($data, $dataFilter, $id);
        } else {
            $this->indexItem($data, $dataFilter, $id);
        }
    }

    public function indexItems(iterable $items, callable|array|null $dataFilter = null, callable|string $id): void
    {
        foreach ($items as $item) {
            $this->indexItem($item, $dataFilter, $id);
        }
    }

    public function indexItem(object $item, callable|array|null $dataFilter = null, callable|string $id): void
    {
        if (is_null($item)) {
            return;
        }

        $arrItem = $item instanceof Arrayable ? $item->toArray() : (array) $item;
        $currentValues = match (true) {
            is_callable($dataFilter) => $dataFilter($item),
            is_null($dataFilter)     => $arrItem,
            default                  => Arr::only($arrItem, $dataFilter),
        };

        if (is_null($currentValues)) {
            return;
        }

        $currentId = is_callable($id)
            ? $id($item)
            : $item[$id];

        if (is_null($currentId)) {
            return;
        }

        IndexJob::dispatch($this->index, $currentId, $currentValues);
    }

    public function prepare(string $indexName, array $mapping = [], array $settings = []): void
    {
        $this->createAlias($indexName);
        $this->createIndex($this->index, $mapping, $settings);
        $this->prepared = true;
    }

    public function finish(): void
    {
        $this->switchAlias($this->alias, $this->index);
        $this->prepared = false;
    }

    public function abort(): void
    {
        $this->deleteIndex($this->index);
        $this->prepared = false;
    }

    public function createAlias(string $indexName): void
    {
        $this->alias = $indexName;
        $this->index = $this->alias . '_' . Carbon::now()->format('YmdHis');
    }

    public function createIndex(string $index, array $mapping = [], array $settings = []): void
    {
        $this->elasticsearch->indices()->create([
            'index' => $index,
            'body'  => array_filter([
                'mappings' => $mapping,
                'settings' => $settings,
            ]),
        ]);
    }

    public function switchAlias(string $alias, string $index): void
    {
        $this->elasticsearch->indices()->putAlias([
            'index' => $index,
            'name'  => $alias,
        ]);

        $aliases = $this->elasticsearch->indices()->getAlias(['name' => $alias]);
        foreach ($aliases as $indexLinkedToAlias => $aliasData) {
            if ($indexLinkedToAlias != $index) {
                $this->elasticsearch->indices()->deleteAlias([
                    'index' => $indexLinkedToAlias,
                    'name'  => $alias,
                ]);
                $this->elasticsearch->indices()->delete(['index' => $indexLinkedToAlias]);
            }
        }
    }
}
