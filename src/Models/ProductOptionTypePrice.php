<?php

namespace Rapidez\Core\Models;

class ProductOptionTypePrice extends Model
{
    protected $table = 'catalog_product_option_type_price';

    protected $primaryKey = 'option_type_price_id';

    public $timestamps = false;

    public function store()
    {
        return $this->belongsTo(config('rapidez.models.store'));
    }

    public function value()
    {
        return $this->belongsTo(config('rapidez.models.product_option_type_value'), 'option_type_id');
    }
}
