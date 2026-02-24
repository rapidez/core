<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Facades\Rapidez;

class ProductStock extends Model
{
    protected $table = 'cataloginventory_stock_item';

    protected $casts = [
        'is_in_stock' => 'boolean',
    ];

    public function getHidden()
    {
        $hidden = parent::getHidden();

        if (! config('rapidez.system.expose_stock')) {
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

    protected function minSaleQty(): Attribute
    {
        return Attribute::get(function (?float $value): ?float {
            $increments = $this->qty_increments ?: 1;
            $minSaleQty = $value ?: 1;

            return ($minSaleQty - fmod($minSaleQty, $increments)) ?: $increments;
        });
    }
}
