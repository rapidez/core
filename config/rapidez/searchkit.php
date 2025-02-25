<?php

// See: https://www.searchkit.co/docs/api-documentation/searchkit#search_settings-configuration
return [
    // Attributes that are used to highlight the search results.
    'highlight_attributes' => [
        'name',
    ],

    // Additional attributes that are used to search the results.
    // This will be merged with the searchable
    // attributes configured in Magento.
    'search_attributes' => [
        // ['field' => 'attribute_code', 'weight' => 4.0],
    ],

    // Attributes that are returned in the search result response.
    // Don't want to keep track of this? An empty array will
    // return all attributes, but that's not recommended!
    'result_attributes' => [
        'entity_id',
        'name',
        'sku',
        'price',
        'special_price',
        'image',
        'images',
        'url',
        'thumbnail',
        'in_stock',
        'children',
        'super_*',
        'reviews_count',
        'reviews_score',
    ],

    // Additional attributes that are used to create facets.
    // From Magento only "Yes/No, Dropdown, Multiple Select and Price" attribute types
    // can be configured as filter. If you'd like to have a filter for an attribute
    // with, for example, the type of "Text", you can specify the attribute_code here.
    'facet_attributes' => [
        // ['attribute' => 'brand', 'field' => 'brand.keyword', 'type' => 'string'],
    ],

    // Additional sorting options to be added to the product listings
    // Given directions can only be an array of 'asc' and/or 'desc'
    // Order shown here will be the order shown in the dropdown (including the order of the given directions!)
    'additional_sorting' => [
        'created_at' => ['desc'],
    ],

    // Attributes that are used to create filters.
    // Required so that SearchKit can keep track of the type and field of each attribute.
    // TODO: Do the undocumented nestedPath and filterQuery parameters have any use case for us?
    'filter_attributes' => [
        ['attribute' => 'entity_id', 'field' => 'entity_id', 'type' => 'numeric'],
        ['attribute' => 'category_ids', 'field' => 'category_ids', 'type' => 'numeric'],
        ['attribute' => 'visibility', 'field' => 'visibility', 'type' => 'numeric'],
    ],
];
