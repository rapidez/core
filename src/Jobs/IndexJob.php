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

    /** @var array<string, mixed> */
    protected array $values;

    /** @param iterable<string, mixed>|Arrayable<string, mixed> $values */
    public function __construct(string $index, int|string $id, iterable|Arrayable $values)
    {
        $this->index = $index;
        $this->id = $id;
        $this->values = ($values instanceof Arrayable ? $values->toArray() : (array) $values);
    }

    public function handle(Elasticsearch $elasticsearch): void
    {
        // @phpstan-ignore-next-line
        $elasticsearch->index([
            'index' => $this->index,
            'id'    => $this->id,
            'body'  => $this->values,
        ]);
    }
}
