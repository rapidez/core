<?php
return [
    // Default store, see middleware: DetermineAndSetShop
    'store' => env('STORE', 1),

    // Elasticsearch url.
    'es_url' => env('ELASTICSEARCH_URL', 'http://localhost:9200'),

    // Elasticsearch prefix.
    'es_prefix' => env('ELASTICSEARCH_PREFIX', 'rapidez'),

    // Media url.
    'media_url' => env('MEDIA_URL', env('MAGENTO_URL') . '/media'),

    // Magento url.
    'magento_url' => env('MAGENTO_URL'),

    // The variables which should be exposed to the frontend.
    'exposed' => [
        'store',
        'es_url',
        'es_prefix',
        'media_url',
        'magento_url',
    ],

    // Should the routes be registered?
    'routes' => true,
];
