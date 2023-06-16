<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Illuminate\Support\Arr;
use Rapidez\Core\Jobs\IndexJob;

class ElasticsearchIndexer
{
    public string $alias;
    public string $index;

    protected Elasticsearch $elasticsearch;

    public function __construct(Elasticsearch $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function deleteIndex(string $index): void
    {
        $this->elasticsearch->indices()->delete(['index' => $index]);
    }

    public function index(iterable|object $data, callable|array $mapping, callable|string $id = 'id'): void
    {
        if (is_iterable($data)) {
            $this->indexItems($data, $mapping, $id);
        } else {
            $this->indexItem($data, $mapping, $id);
        }
    }

    public function indexItems(iterable $items, callable|array $mapping, callable|string $id = 'id'): void
    {
        foreach ($items as $item) {
            $this->indexItem($item, $mapping, $id);
        }
    }

    public function indexItem(object $item, callable|array $mapping, callable|string $id = 'id'): void
    {
        if (is_null($item)) {
            return;
        }

        $currentValues = is_callable($mapping)
            ? $mapping($item)
            : Arr::only((array) $item, $mapping);

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
    }

    public function finish(): void
    {
        $this->switchAlias($this->alias, $this->index);
    }

    public function abort(): void
    {
        $this->deleteIndex($this->index);
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
