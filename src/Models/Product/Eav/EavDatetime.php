<?php

namespace Rapidez\Core\Models\Product\Eav;

use Rapidez\Core\Models\Product\EavAttribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Model;

class EavDatetime extends AbstractEav
{
    protected $table = 'catalog_product_entity_datetime';

    protected $guarded = [];

    protected $casts = [
        'value' => 'datetime',
    ];
}
