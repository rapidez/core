<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductView extends Model
{
    const CREATED_AT = 'added_at';

    const UPDATED_AT = null;

    protected $table = 'report_viewed_product_index';

    protected $primaryKey = 'index_id';

    protected static function booted(): void
    {
        static::saving(function (ProductView $productView) {
            $productView->store_id = config('rapidez.store');
        });

        static::addGlobalScope('store', function (Builder $builder) {
            $builder->where('store_id', config('rapidez.store'));
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
