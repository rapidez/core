<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryProduct extends Model
{
    protected $primaryKey = 'entity_id';

    public function getTable()
    {
        return 'catalog_category_product_index_store' . config('rapidez.store');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.category'), 'category_id', 'entity_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.product'), 'product_id', 'entity_id');
    }
}
