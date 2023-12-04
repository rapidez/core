<?php

return [
    // Which product visibilities should be indexed?
    // VISIBILITY_NOT_VISIBLE    = 1
    // VISIBILITY_IN_CATALOG     = 2
    // VISIBILITY_IN_SEARCH      = 3
    // VISIBILITY_BOTH           = 4
    'visibility' => [2, 3, 4],

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

    // Index the positions per category
    // This option enables the magento functionality where products are sortable per category.
    // You might want to disable this if you have a lot of categories, as each category results in a new mapping being created.
    'category_positions' => true,
];
