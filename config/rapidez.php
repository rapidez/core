<?php

return [
    // Default store, see middleware: DetermineAndSetShop
    'store' => env('STORE', 1),

    // Elasticsearch url.
    'es_url' => env('ELASTICSEARCH_URL', 'http://localhost:9200'),

    // Elasticsearch prefix.
    'es_prefix' => env('ELASTICSEARCH_PREFIX', 'rapidez'),

    // Media url.
    'media_url' => env('MEDIA_URL', env('MAGENTO_URL').'/media'),

    // Magento url.
    'magento_url' => env('MAGENTO_URL'),

    // Magento crypt key.
    'crypt_key' => env('CRYPT_KEY'),

    // The variables which should be exposed to the frontend.
    'exposed' => [
        'store',
        'es_url',
        'es_prefix',
        'media_url',
        'magento_url',
        'notifications',
        'checkout_steps',
        'flushable_localstorage_keys',
    ],

    // Additional searchable attributes with the search weight.
    'searchable' => [
        // 'attribute' => 4.0,
    ],

    // Should the stock qty be exposed and indexed within Elasticsearch?
    'expose_stock' => false,

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
        'page'     => Rapidez\Core\Http\Controllers\PageController::class,
        'product'  => Rapidez\Core\Http\Controllers\ProductController::class,
        'category' => Rapidez\Core\Http\Controllers\CategoryController::class,
    ],

    // The fully qualified class names of the models.
    'models' => [
        'page'         => Rapidez\Core\Models\Page::class,
        'attribute'    => Rapidez\Core\Models\Attribute::class,
        'product'      => Rapidez\Core\Models\Product::class,
        'category'     => Rapidez\Core\Models\Category::class,
        'config'       => Rapidez\Core\Models\Config::class,
        'optionswatch' => Rapidez\Core\Models\OptionSwatch::class,
        'optionvalue'  => Rapidez\Core\Models\OptionValue::class,
        'productimage' => Rapidez\Core\Models\ProductImage::class,
        'quote'        => Rapidez\Core\Models\Quote::class,
        'rewrite'      => Rapidez\Core\Models\Rewrite::class,
        'store'        => Rapidez\Core\Models\Store::class,
        'widget'       => Rapidez\Core\Models\Widget::class,
        'block'        => Rapidez\Core\Models\Block::class,
    ],

    // The fully qualified class names of the widgets.
    'widgets' => [
        'Magento\Cms\Block\Widget\Block'                   => Rapidez\Core\Widgets\Block::class,
        'Magento\CatalogWidget\Block\Product\ProductsList' => Rapidez\Core\Widgets\ProductList::class,
    ],

    'view_only_widget' => \Rapidez\Core\Widgets\ViewOnly::class,

    // The fully qualified class names of the content variables.
    'content-variables' => [
        Rapidez\Core\ContentVariables\Media::class,
        Rapidez\Core\ContentVariables\Store::class,
        Rapidez\Core\ContentVariables\Widget::class,
    ],

    // Localstorage keys that need to be flushed when the cache is cleared.
    'flushable_localstorage_keys' => [
        'attributes',
        'countries',
        'swatches',
    ],

    // The classes for each notification type.
    'notifications' => [
        'classes' => [
            'warning' => 'text-yellow-600 bg-yellow-100 border-yellow-300',
            'success' => 'text-green-600 bg-green-100 border-green-300',
            'info'    => 'text-blue-600 bg-blue-100 border-blue-300',
            'error'   => 'text-red-600 bg-red-100 border-red-300',
        ],
    ],

    // Z-Index per element
    'z-indexes' => [
        'header-dropdowns' => 'z-20',
        'lightbox'         => 'z-30',
        'notification'     => 'z-20',
        'slideover'        => 'z-50',
    ],

];
