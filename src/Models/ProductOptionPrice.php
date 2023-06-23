<?php

namespace Rapidez\Core\Models;

class ProductOptionPrice extends Model
{
    public $timestamps = false;

    protected $table = 'catalog_product_option_price';

    protected $primaryKey = 'option_price_id';

    public function store()
    {
        return $this->belongsTo(config('rapidez.models.store'));
    }

    public function option()
    {
        return $this->belongsTo(config('rapidez.models.product_option'), 'option_id');
    }
}
