<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductTierPrice extends Model
{
    protected $table = 'catalog_product_entity_tier_price';

    protected $primaryKey = 'value_id';

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(config('rapidez.models.product'), 'entity_id');
    }
}
