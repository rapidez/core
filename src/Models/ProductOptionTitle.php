<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionTitle extends Model
{
    protected $table = 'catalog_product_option_title';

    protected $primaryKey = 'option_title_id';

    public $timestamps = false;

    /** @return BelongsTo<Store, ProductOptionTitle> */
    public function store(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.store'));
    }

    /** @return BelongsTo<ProductOption, ProductOptionTitle> */
    public function option(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.product_option'), 'option_id');
    }
}
