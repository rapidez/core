<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $price
 * @property string $price_type
 */
class ProductOptionPrice extends Model
{
    protected $table = 'catalog_product_option_price';

    protected $primaryKey = 'option_price_id';

    public $timestamps = false;

    /** @return BelongsTo<Store, ProductOptionPrice> */
    public function store(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.store'));
    }

    /** @return BelongsTo<ProductOption, ProductOptionPrice> */
    public function option(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.product_option'), 'option_id');
    }
}
