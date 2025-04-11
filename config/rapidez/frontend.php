<?php

return [
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
        'cart_attributes',
    ],

    // The attribute codes of product attributes used in the cart, used to fetch option values
    'cart_attributes' => [
        // 'manufacturer',
        // 'material',
    ],

    // The checkout steps which are used to name the steps
    // in the url and in the progressbar on steps. You can
    // add different steps for different stores. Keep
    // them lowercase and do not include any spaces.
    'checkout_steps' => [
        // 'default' => ['onestep'],
        'default' => ['login', 'credentials', 'payment'],
    ],

    'autocomplete' => [
        // Attach additional indexes to the autocomplete
        // Uses the views in rapidez::layouts.partials.header.autocomplete
        'additionals' => [
            'categories' => ['name^3', 'description'],

            // For example:
            // 'blogs' => [
            //     'fields' => ['title^3', 'description'],  // Required
            //     'size' => 3,                             // Optional; Overrides the default `size` as defined below
            //     'stores' => ['my_second_store'],         // Optional; Define this only if you want to specify which stores use this index
            //     'sort' => ['date' => 'desc'],            // Optional; See: https://www.elastic.co/guide/en/elasticsearch/reference/7.17/sort-search-results.html)
            // ],
        ],

        'debounce' => 500,
        'size'     => 3,
    ],

    // Link store codes to theme folders
    // The structure is `'store_code' => 'folder_path'`
    'themes' => [
        'default' => resource_path('themes/default'),
    ],

    // The fully qualified class names of the widgets.
    'widgets' => [
        'Magento\Cms\Block\Widget\Block'                   => Rapidez\Core\Widgets\Block::class,
        'Magento\CatalogWidget\Block\Product\ProductsList' => Rapidez\Core\Widgets\ProductList::class,
    ],

    'view_only_widget' => \Rapidez\Core\Widgets\ViewOnly::class,

    // The fully qualified class names of the content variables.
    'content_variables' => [
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

    // Add to cart settings to automatically select configurable- or product options (true/false)
    'add_to_cart' => [
        'auto_select_configurable_options' => false,
        'auto_select_product_options'      => false,
    ],

    // The path to redirect to after a failed checkout
    'checkout_success_fail_redirect_path' => 'cart',
];
