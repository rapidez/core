<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rapidez\Core\Models\Scopes\ForCurrentStoreWithoutLimitScope;

class ProductImageValue extends Model
{
    protected $table = 'catalog_product_entity_media_gallery_value';

    protected $primaryKey = 'record_id';

    public $timestamps = false;

    protected static function booted(): void
    {
        static::addGlobalScope(new ForCurrentStoreWithoutLimitScope('value_id'));
    }

    /** @return BelongsTo<ProductImage, ProductImageValue> */
    public function productImage(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.product_image'), 'value_id');
    }
}
