<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReviewSummary extends Model
{
    protected $table = 'review_entity_summary';

    protected $primaryKey = 'primary_id';

    public $timestamps = false;

    protected static function booting(): void
    {
        static::addGlobalScope('only-product-reviews', fn (Builder $builder) => $builder
            ->where('entity_type', 1)
        );

        static::addGlobalScope('store', fn (Builder $builder) => $builder
            ->where('store_id', config('rapidez.store'))
        );
    }

    /** @return BelongsTo<Product, ProductReviewSummary> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.product'), 'entity_pk_value');
    }
}
