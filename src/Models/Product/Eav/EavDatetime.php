<?php

namespace Rapidez\Core\Models\Product\Eav;

class EavDatetime extends AbstractEav
{
    protected $table = 'catalog_product_entity_datetime';

    protected $guarded = [];

    protected $casts = [
        'value' => 'datetime',
    ];
}
