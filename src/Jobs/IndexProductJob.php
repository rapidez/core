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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Elasticsearch $elasticsearch)
    {
        $elasticsearch->index([
            'index' => config('rapidez.es_prefix') . '_products_' . $this->data['store'],
            'id'    => $this->data['entity_id'],
            'body'  => $this->data,
        ]);
    }
}
