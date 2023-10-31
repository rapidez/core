<?php

return [
    // Which product visibilities should we limit the ElasticSearch product indexer to?
    'index_visibility' => [2, 3, 4],

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
