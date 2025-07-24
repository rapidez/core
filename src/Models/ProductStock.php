<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Facades\Rapidez;

class ProductStock extends Model
{
    protected $table = 'cataloginventory_stock_item';

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('onlyExposedColumns', fn(Builder $builder) => $builder->select(['product_id', ...config('rapidez.exposed_stock_columns')]));
    }

    public function __get($key)
    {
        if ($this->hasAttribute('use_config_' . $key) && $this->getAttribute('use_config_' . $key) == 1) {
            return Rapidez::config('cataloginventory/item_options/' . $key);
        }

        return parent::__get($key);
    }
}
