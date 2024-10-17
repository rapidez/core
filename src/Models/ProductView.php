<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $store_id
 * @property int $product_id
 */
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
                'store_id'      => config('rapidez.store'),
                'event_type_id' => 1, // Product Viewed
                'object_id'     => $productView->product_id,
                'subtype'       => 0,
                'subject_id'    => 0,
            ]);
        });

        static::addGlobalScope('store', function (Builder $builder) {
            $builder->where('store_id', config('rapidez.store'));
        });
    }

    /** @return BelongsTo<Product, ProductView> */
    public function product(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.product'), 'product_id');
    }

    /** @return BelongsTo<Store, ProductView> */
    public function store(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.store'), 'store_id');
    }
}
