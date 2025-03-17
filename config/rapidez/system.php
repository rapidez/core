<?php

return [
    // Default store, see middleware: DetermineAndSetShop
    'store' => env('STORE', 1),

    // Elasticsearch url.
    'es_url' => env('ELASTICSEARCH_URL', 'http://localhost:9200'),

    // Elasticsearch prefix.
    'es_prefix' => env('ELASTICSEARCH_PREFIX', 'rapidez'),

    // Get Magento url from Database
    'magento_url_from_db' => env('GET_MAGENTO_URL_FROM_DATABASE', false),

    // Media url.
    'media_url' => env('MEDIA_URL', env('MAGENTO_URL') . '/media'),

    // Magento url.
    'magento_url' => env('MAGENTO_URL'),

    // Magento crypt key.
    'crypt_key' => env('CRYPT_KEY'),

    // With this token you can run commands from an url.
    'admin_token' => env('RAPIDEZ_TOKEN', env('APP_KEY')),

    // Should the stock qty be exposed and indexed within Elasticsearch?
    'expose_stock' => false,

    'standalone_checkout' => [
        // What cache store should be used to store temporary standalone checkout credentials
        'cache_store' => env('STANDALONE_CHECKOUT_CACHE_STORE', config('cache.default')),
        'success_fail_redirect_path' => 'cart',
    ],
];
