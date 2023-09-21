<?php

namespace Rapidez\Core\Listeners;

use Illuminate\Support\Facades\Event;

class ElasticsearchHealthcheck
{
    protected $esVersion = '7.6';

    public function handle()
    {
        $response = [
            'healthy' => true,
            'messages' => [],
        ];

        $data = json_decode(@file_get_contents(config('rapidez.es_url')));
        if (is_object($data)
            && property_exists($data, 'version')
            && $data->version->build_flavor !== 'oss'
            && ! version_compare($data->version->number, $this->esVersion, '>=')
        ) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type' => 'error',
                'value' => __('Your Elasticsearch version is too low!') . PHP_EOL .
                    __('Your version: :version (:build_flavour)', ['version' => $data->version->number, 'build_flavour' => $data->version->build_flavor]) . PHP_EOL .
                    __('You need at least: :version basic/default (non OSS version)', ['version' => $this->esVersion])
            ];
        } elseif (! is_object($data) || ! property_exists($data, 'version')) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type' => 'error',
                'value' => __('Elasticsearch is not reachable at: :url', ['url' => config('rapidez.es_url')])
            ];
        }

        return $response;
    }

    public static function register()
    {
        Event::listen('rapidez:health-check', static::class);
    }
}
