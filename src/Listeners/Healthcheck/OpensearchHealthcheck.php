<?php

namespace Rapidez\Core\Listeners\Healthcheck;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OpensearchHealthcheck extends Base
{
    public function handle()
    {
        $response = [
            'healthy'  => true,
            'messages' => [],
        ];

        if (config('elasticsearch.backend_type') !== 'opensearch') {
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
                'value' => __('Opensearch is not reachable at: :url', ['url' => config('rapidez.es_url')]),
            ];

            return $response;
        }

        if (! property_exists($data->version, 'distribution')
            || $data->version->distribution !== 'opensearch'
        ) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type'  => 'error',
                'value' => __('SCOUT_SEARCH_BACKEND is set to opensearch, but the given ELASTICSEARCH_URL does not seem to be opensearch!'),
            ];
        }

        return $response;
    }
}
