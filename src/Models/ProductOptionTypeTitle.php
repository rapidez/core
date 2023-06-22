<?php

namespace Rapidez\Core\Models;

class ProductOptionTypeTitle extends Model
{
    protected $table = 'catalog_product_option_type_title';

    protected $primaryKey = 'option_type_title_id';

    public $timestamps = false;

    public function store()
    {
        return $this->belongsTo(config('rapidez.models.store'));
    }

    public function type_value()
    {
        return $this->belongsTo(config('rapidez.models.product_option_type_value'), 'option_type_id');
    }
}
