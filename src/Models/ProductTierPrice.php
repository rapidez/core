<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTierPrice extends Model
{
    public $timestamps = false;

    protected $table = 'catalog_product_entity_tier_price';

    protected $primaryKey = 'value_id';

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            config('rapidez.models.product'),
            'entity_id'
        );
    }
}
