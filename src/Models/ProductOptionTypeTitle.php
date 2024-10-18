<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionTypeTitle extends Model
{
    protected $table = 'catalog_product_option_type_title';

    protected $primaryKey = 'option_type_title_id';

    public $timestamps = false;

    /** @return BelongsTo<Store, ProductOptionTypeTitle> */
    public function store(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.store'));
    }

    /** @return BelongsTo<ProductOptionTypeValue, ProductOptionTypeTitle> */
    public function value(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.product_option_type_value'), 'option_type_id');
    }
}
