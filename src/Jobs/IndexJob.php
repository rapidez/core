<?php

namespace Rapidez\Core\Jobs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Bus\Dispatchable;
use MailerLite\LaravelElasticsearch\Manager as Elasticsearch;

class IndexJob
{
    use Dispatchable;

    protected string $index;
    protected int|string $id;
    protected array $values;

    public function __construct(string $index, int|string $id, $values)
    {
        $this->index = $index;
        $this->id = $id;
        $this->values = ($values instanceof Arrayable ? $values->toArray() : (array) $values);
    }

    public function handle(Elasticsearch $elasticsearch)
    {
        $elasticsearch->index([
            'index' => $this->index,
            'id'    => $this->id,
            'body'  => $this->values,
        ]);
    }
}
