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
        'checkout_steps',
        'storage_flushable'
    ],

    // With this token you can run commands from an url.
    'admin_token' => env('RAPIDEZ_TOKEN', env('APP_KEY')),

    // The checkout steps which are used to name the steps
    // in the url and in the progressbar on steps. Keep
    // them lowercase and do not include any spaces.
    'checkout_steps' => ['cart', 'login', 'credentials', 'payment', 'success'],

    // Should the routes be registered? The controllers
    // below will not be used anymore when disabled.
    'routes' => true,

    // The fully qualified class names of the controllers.
    'controllers' => [
        'page' => Rapidez\Core\Http\Controllers\PageController::class,
        'product' => Rapidez\Core\Http\Controllers\ProductController::class,
        'category' => Rapidez\Core\Http\Controllers\CategoryController::class,
    ],

    // Local storage keys that need to be flushed
    'storage_flushable' => [
        'countries',
        'swatches',
        'attributes'
    ]
];
