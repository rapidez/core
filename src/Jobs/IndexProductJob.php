<?php

namespace Rapidez\Core\Jobs;

use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Illuminate\Foundation\Bus\Dispatchable;

class IndexProductJob
{
    use Dispatchable;

    protected string $index;

    protected array $data;

    public function __construct(string $index, array $data)
    {
        $this->index = $index;
        $this->data = $data;
    }

    public function handle(Elasticsearch $elasticsearch)
    {
        $elasticsearch->index([
            'index' => $this->index,
            'id'    => $this->data['id'],
            'body'  => $this->data,
        ]);
    }
}
