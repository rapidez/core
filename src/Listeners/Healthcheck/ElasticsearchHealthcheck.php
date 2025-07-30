<?php

namespace Rapidez\Core\Listeners\Healthcheck;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ElasticsearchHealthcheck extends Base
{
    protected $esVersion = '7.6';

    public function handle()
    {
        $response = [
            'healthy'  => true,
            'messages' => [],
        ];

        if (config('elasticsearch.backend_type') !== 'elasticsearch') {
            return $response;
        }

        try {
            $data = Http::withBasicAuth(
                config('elasticsearch.user'),
                config('elasticsearch.password')
            )
                ->get(config('rapidez.es_url'))
                ->object();
        } catch (ConnectionException $e) {
            $data = null;
        }

        if (! is_object($data) || ! property_exists($data, 'version')) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type'  => 'error',
                'value' => __('Elasticsearch is not reachable at: :url', ['url' => config('rapidez.es_url')]),
            ];

            return $response;
        }

        if (property_exists($data->version, 'distribution')
            && $data->version->distribution === 'opensearch'
        ) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type'  => 'error',
                'value' => __('SCOUT_SEARCH_BACKEND is set to elasticsearch, but the given ELASTICSEARCH_URL seems to be opensearch!'),
            ];

            return $response;
        }

        if ($data->version->build_flavor !== 'oss'
            && ! version_compare($data->version->number, $this->esVersion, '>=')
        ) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type'  => 'error',
                'value' => __('Your Elasticsearch version is too low!') . PHP_EOL .
                    __('Your version: :version (:build_flavour)', ['version' => $data->version->number, 'build_flavour' => $data->version->build_flavor]) . PHP_EOL .
                    __('You need at least: :version basic/default (non OSS version)', ['version' => $this->esVersion]),
            ];
        }

        return $response;
    }
}
