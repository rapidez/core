<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $price
 * @property string $price_type
 */
class ProductOptionTypePrice extends Model
{
    protected $table = 'catalog_product_option_type_price';

    protected $primaryKey = 'option_type_price_id';

    public $timestamps = false;

    /** @return BelongsTo<Store, ProductOptionTypePrice> */
    public function store(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.store'));
    }

    /** @return BelongsTo<ProductOptionTypeValue, ProductOptionTypePrice> */
    public function value(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.product_option_type_value'), 'option_type_id');
    }
}
