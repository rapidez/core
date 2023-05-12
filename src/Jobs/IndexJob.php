<?php

namespace Rapidez\Core\Jobs;

use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Illuminate\Foundation\Bus\Dispatchable;

class IndexJob
{
    use Dispatchable;

    protected string $index;
    protected int $id;
    protected array $values;

    public function __construct(string $index, int $id, array $values)
    {
        $this->index = $index;
        $this->id = $id;
        $this->values = $values;
    }

    public function handle(Elasticsearch $elasticsearch)
    {
        $elasticsearch->index([
            'index' => $this->index,
            'id' => $this->id,
            'body' => $this->values,
        ]);
    }
}
