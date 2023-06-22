<?php

namespace Rapidez\Core\Models;

class ProductOptionTypeValue extends Model
{
    public $timestamps = false;
    protected $table = 'catalog_product_option_type_value';

    protected $primaryKey = 'option_type_id';

    public function option()
    {
        return $this->belongsTo(config('rapidez.models.product_option'), 'option_id');
    }

    public function titles()
    {
        return $this->hasMany(config('rapidez.models.product_option_type_title'), 'option_type_id');
    }
}
