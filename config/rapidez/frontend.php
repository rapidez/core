<?php

return [
    // The variables which should be exposed to the frontend.
    'exposed' => [
        'store',
        'es_url',
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
    // in the url and in the progressbar on steps. Keep
    // them lowercase and do not include any spaces.
    'checkout_steps' => [
        'login', 'credentials', 'payment',
        // 'onestep',
    ],

    'autocomplete' => [
        // Attach additional indexes to the autocomplete
        // Uses the views in rapidez::layouts.partials.header.autocomplete
        'additionals' => [
            'categories' => [],

            // For example:
            // 'blogs' => [
            //     'size' => 3,                             // Optional; Overrides the default `size` as defined below
            // ],
        ],

        'size' => 3,
    ],

    // Link store codes to theme folders
    // The structure is `'store_code' => 'folder_path'`
    'theme' => resource_path('themes/default'),

    // The fully qualified class names of the widgets.
    'widgets' => [
        'Magento\Cms\Block\Widget\Block'                   => Rapidez\Core\Widgets\Block::class,
        'Magento\CatalogWidget\Block\Product\ProductsList' => Rapidez\Core\Widgets\ProductList::class,
        'Magento\Catalog\Block\Category\Widget\Link'       => Rapidez\Core\Widgets\ProductAndCategoryLink::class,
        'Magento\Catalog\Block\Product\Widget\Link'        => Rapidez\Core\Widgets\ProductAndCategoryLink::class,
        'Magento\Cms\Block\Widget\Page\Link'               => Rapidez\Core\Widgets\PageLink::class,
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
