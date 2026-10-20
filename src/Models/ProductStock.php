<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Rapidez\Core\Facades\Rapidez;

class ProductStock extends Model
{
    protected $table = 'cataloginventory_stock_item';

    protected $casts = [
        'is_in_stock'    => 'boolean',
        'qty_increments' => 'float',
        'backorders'     => 'integer',
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
            $value = Rapidez::config('cataloginventory/item_options/' . $key);

            return $this->hasCast($key) ? $this->castAttribute($key, $value) : $value;
        }

        return parent::__get($key);
    }

    protected function minSaleQty(): Attribute
    {
        return Attribute::get(function (?float $value): ?float {
            $increments = (float) $this->qty_increments ?: 1;
            $minSaleQty = $value ?: 1;

            return ($minSaleQty - fmod($minSaleQty, $increments)) ?: $increments;
        });
    }
}
