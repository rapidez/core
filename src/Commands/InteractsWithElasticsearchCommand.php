<?php

namespace Rapidez\Core\Commands;

use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Illuminate\Console\Command;
use Rapidez\Core\Models\Store;

abstract class InteractsWithElasticsearchCommand extends Command
{
    protected Elasticsearch $elasticsearch;

    public function __construct(Elasticsearch $elasticsearch)
    {
        parent::__construct();

        $this->elasticsearch = $elasticsearch;
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

    public function deleteIndex(string $index): void
    {
        $this->elasticsearch->indices()->delete(['index' => $index]);
    }
}
