<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Facades\Rapidez;

class ProductStock extends Model
{
    protected $table = 'cataloginventory_stock_item';

    public function getHidden()
    {
        $hidden = parent::getHidden();

        if (!config('rapidez.system.expose_stock')) {
            $hidden[] = 'qty';
        }

        return $hidden;
    }

    public function __get($key)
    {
        if ($this->hasAttribute('use_config_' . $key) && $this->getAttribute('use_config_' . $key) == 1) {
            return Rapidez::config('cataloginventory/item_options/' . $key);
        }

        return parent::__get($key);
    }
}
