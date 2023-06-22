<?php

namespace Rapidez\Core\Models;

class ProductOptionTypeValue extends Model
{
    protected $table = 'catalog_product_option_type_value';

    protected $primaryKey = 'option_type_id';

    public $timestamps = false;

    public function option()
    {
        return $this->belongsTo(config('rapidez.models.product_option'), 'option_id');
    }

    public function titles()
    {
        return $this->hasMany(config('rapidez.models.product_option_type_title'), 'option_type_id');
    }
}
