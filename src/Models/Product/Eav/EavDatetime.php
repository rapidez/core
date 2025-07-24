<?php

namespace Rapidez\Core\Models\Product\Eav;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Product\EavAttribute;

class EavDatetime extends AbstractEav
{
    protected $table = 'catalog_product_entity_datetime';

    protected $guarded = [];

    protected $casts = [
        'value' => 'datetime',
    ];
}
