<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductView extends Model
{
    protected $table = 'report_viewed_product_index';

    protected $primaryKey = 'index_id';

    const CREATED_AT = 'added_at';

    const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::saving(function (ProductView $productView) {
            $productView->store_id = config('rapidez.store');
        });

        static::created(function (ProductView $productView) {
            $reportEventModel = config('rapidez.models.report_event');

            $reportEventModel::create([
                'store_id' => config('rapidez.store'),
                'event_type_id' => 1, // Product Viewed
                'object_id' => $productView->product_id,
                'subtype' => 0,
                'subject_id' => 0,
            ]);
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
