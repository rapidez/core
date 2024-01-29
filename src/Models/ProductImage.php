<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductImage extends Model
{
    protected $table = 'catalog_product_entity_media_gallery';

    protected $primaryKey = 'value_id';

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

    public function productImageValue()
    {
        return $this->belongsTo(config('rapidez.models.product_image_value'), 'value_id', 'value_id');
    }
}
