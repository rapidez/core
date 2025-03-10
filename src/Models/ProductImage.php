<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $table = 'catalog_product_entity_media_gallery';

    protected $primaryKey = 'value_id';

    protected $with = ['productImageValue'];

    protected static function booted(): void
    {
        static::addGlobalScope(
            'enabled',
            fn (Builder $query) => $query
                ->where($query->qualifyColumn('disabled'), 0)
                ->whereHas(
                    'productImageValue',
                    fn ($query) => $query
                        ->where($query->qualifyColumn('disabled'), 0)
                )
        );
    }

    public function productImageValue(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.product_image_value'), 'value_id', 'value_id');
    }
}
