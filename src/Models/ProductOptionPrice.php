<?php

namespace Rapidez\Core\Models;

class ProductOptionPrice extends Model
{
    protected $table = 'catalog_product_option_price';

    protected $primaryKey = 'option_price_id';

    public $timestamps = false;

    public function store()
    {
        return $this->belongsTo(config('rapidez.models.store'));
    }

    public function option()
    {
        return $this->belongsTo(config('rapidez.models.product_option'), 'option_id');
    }
}
