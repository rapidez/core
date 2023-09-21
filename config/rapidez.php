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
        'customer_fields_show',
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
    // in the url and in the progressbar on steps. You can
    // add different steps for different stores. Keep
    // them lowercase and do not include any spaces.
    'checkout_steps' => [
        'default' => ['cart', 'login', 'credentials', 'payment', 'success'],
    ],

    // Should the routes be registered? The controllers
    // below will not be used anymore when disabled.
    'routes' => true,

    'fallback_routes' => [
        // How long (in seconds) it should cache which controller handles which route.
        // null means cache forever, 0 means never cache. customisation is possible using a closure.
        // This does not cache the response, it caches the controller used for that page.
        'cache_duration' => 3600,
    ],

    // Link store codes to theme folders
    // The structure is `'store_code' => 'folder_path'`
    'themes' => [
        'default' => resource_path('themes/default'),
    ],

    // The fully qualified class names of the controllers.
    'controllers' => [
        'page'     => Rapidez\Core\Http\Controllers\PageController::class,
        'product'  => Rapidez\Core\Http\Controllers\ProductController::class,
        'category' => Rapidez\Core\Http\Controllers\CategoryController::class,
    ],

    // The fully qualified class names of the models.
    'models' => [
        'page'                      => Rapidez\Core\Models\Page::class,
        'attribute'                 => Rapidez\Core\Models\Attribute::class,
        'product'                   => Rapidez\Core\Models\Product::class,
        'category'                  => Rapidez\Core\Models\Category::class,
        'config'                    => Rapidez\Core\Models\Config::class,
        'option_swatch'             => Rapidez\Core\Models\OptionSwatch::class,
        'option_value'              => Rapidez\Core\Models\OptionValue::class,
        'product_image'             => Rapidez\Core\Models\ProductImage::class,
        'product_view'              => Rapidez\Core\Models\ProductView::class,
        'product_option'            => Rapidez\Core\Models\ProductOption::class,
        'product_option_title'      => Rapidez\Core\Models\ProductOptionTitle::class,
        'product_option_price'      => Rapidez\Core\Models\ProductOptionPrice::class,
        'product_option_type_title' => Rapidez\Core\Models\ProductOptionTypeTitle::class,
        'product_option_type_price' => Rapidez\Core\Models\ProductOptionTypePrice::class,
        'product_option_type_value' => Rapidez\Core\Models\ProductOptionTypeValue::class,
        'quote'                     => Rapidez\Core\Models\Quote::class,
        'quote_item'                => Rapidez\Core\Models\QuoteItem::class,
        'quote_item_option'         => Rapidez\Core\Models\QuoteItemOption::class,
        'rewrite'                   => Rapidez\Core\Models\Rewrite::class,
        'store'                     => Rapidez\Core\Models\Store::class,
        'widget'                    => Rapidez\Core\Models\Widget::class,
        'block'                     => Rapidez\Core\Models\Block::class,
        'sales_order'               => Rapidez\Core\Models\SalesOrder::class,
        'sales_order_address'       => Rapidez\Core\Models\SalesOrderAddress::class,
        'sales_order_item'          => Rapidez\Core\Models\SalesOrderItem::class,
        'sales_order_payment'       => Rapidez\Core\Models\SalesOrderPayment::class,
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

    // From Magento only "Yes/No, Dropdown, Multiple Select and Price" attribute types
    // can be configured as filter. If you'd like to have a filter for an attribute
    // with, for example, the type of "Text", you can specify the attribute code here.
    'additional_filters' => [
        // Attribute codes
    ],

    'healthcheck' => [
        // Authenticate using the function passed, if null we will check ips.
        'auth'        => null,
        'allowed_ips' => [
            '127.0.0.1/8',
            ...explode(',', env('HEALTHCHECK_ALLOWED_IPS', '')),
        ],
    ],
];
