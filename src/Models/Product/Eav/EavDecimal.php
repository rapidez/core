<?php

namespace Rapidez\Core\Models\Product\Eav;

class EavDecimal extends AbstractEav
{
    protected $table = 'catalog_product_entity_decimal';

    protected $guarded = [];

    protected $casts = [
        'value' => 'float',
    ];
}
