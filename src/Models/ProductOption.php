<?php

namespace Rapidez\Core\Models;

class ProductOption extends Model
{
    public $timestamps = false;

    protected $table = 'catalog_product_option';

    protected $primaryKey = 'option_id';

    public function product()
    {
        return $this->belongsTo(config('rapidez.models.product'), 'product_id');
    }

    public function titles()
    {
        return $this->hasMany(config('rapidez.models.product_option_title'), 'option_id');
    }

    public function type_values()
    {
        return $this->hasMany(config('rapidez.models.product_option_type_value'), 'option_id');
    }
}
