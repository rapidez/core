<?php

return [
    // TODO: This configuration is a bit lonely here,
    // maybe we can move this to Scout later?

    // Which product visibilities should be indexed?
    // VISIBILITY_NOT_VISIBLE    = 1
    // VISIBILITY_IN_CATALOG     = 2
    // VISIBILITY_IN_SEARCH      = 3
    // VISIBILITY_BOTH           = 4
    'visibility' => [2, 3, 4],

    'models' => [
        'product',
    ],
];
