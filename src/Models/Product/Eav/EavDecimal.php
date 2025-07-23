<?php

namespace Rapidez\Core\Models\Product\Eav;

use Rapidez\Core\Models\Product\EavAttribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Model;

class EavDecimal extends Model
{
    protected $table = 'catalog_product_entity_decimal';

    protected $guarded = [];

    protected $casts = [
        'value' => 'float',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(EavAttribute::class, 'attribute_id', 'attribute_id');
    }
}
