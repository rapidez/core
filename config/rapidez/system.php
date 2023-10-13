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

    // Magento crypt key.
    'crypt_key' => env('CRYPT_KEY'),

    // With this token you can run commands from an url.
    'admin_token' => env('RAPIDEZ_TOKEN', env('APP_KEY')),

    // Additional searchable attributes with the search weight.
    'searchable' => [
        // 'attribute' => 4.0,
    ],

    // From Magento only "Yes/No, Dropdown, Multiple Select and Price" attribute types
    // can be configured as filter. If you'd like to have a filter for an attribute
    // with, for example, the type of "Text", you can specify the attribute_code here.
    'additional_filters' => [
        // eav_attribute attribute_code. e.g. brand
    ],
];
