<?php

namespace Rapidez\Core\Models;

class ProductOptionTypeTitle extends Model
{
    public $timestamps = false;

    protected $table = 'catalog_product_option_type_title';

    protected $primaryKey = 'option_type_title_id';

    public function store()
    {
        return $this->belongsTo(config('rapidez.models.store'));
    }

    public function type_value()
    {
        return $this->belongsTo(config('rapidez.models.product_option_type_value'), 'option_type_id');
    }
}
