<?php

return [
    // Should the routes be registered? The controllers
    // below will not be used anymore when disabled.
    'enabled' => true,

    // The fully qualified class names of the controllers.
    'controllers' => [
        'page'     => Rapidez\Core\Http\Controllers\PageController::class,
        'product'  => Rapidez\Core\Http\Controllers\ProductController::class,
        'category' => Rapidez\Core\Http\Controllers\CategoryController::class,
    ],

    'fallback' => [
        // How long (in seconds) it should cache which controller handles which route.
        // null means cache forever, 0 means never cache. customisation is possible using a closure.
        // This does not cache the response, it caches the controller used for that page.
        'cache_duration' => 3600,
    ],

    'earlyhints' => [
        'enabled' => env('EARLY_HINTS_ENABLED', true),
    ]
];
