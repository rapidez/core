<?php

namespace Rapidez\Core\Jobs;

use Cviebrock\LaravelElasticsearch\Manager as Elasticsearch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    protected string $index;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data, string $index)
    {
        $this->data = $data;
        $this->index = $index;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Elasticsearch $elasticsearch)
    {
        $elasticsearch->index([
            'index' => $this->index,
            'id'    => $this->data['id'],
            'body'  => $this->data,
        ]);
    }
}
