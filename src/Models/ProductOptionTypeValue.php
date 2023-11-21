<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductOptionTypeValue extends Model
{
    protected $table = 'catalog_product_option_type_value';

    protected $primaryKey = 'option_type_id';

    public $timestamps = false;

    protected $with = ['titles', 'prices'];

    protected $appends = ['title', 'price', 'price_label'];

    protected $hidden = ['titles', 'prices'];

    public function option()
    {
        return $this->belongsTo(config('rapidez.models.product_option'), 'option_id');
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->titles->firstForCurrentStore()->title ?? null,
        )->shouldCache();
    }

    public function titles()
    {
        return $this->hasMany(config('rapidez.models.product_option_type_title'), 'option_type_id');
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->prices->firstForCurrentStore(),
        )->shouldCache();
    }

    protected function priceLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! floatval($this->price->price)) {
                    return;
                }

                if ($this->price->price_type == 'percent') {
                    return '+ ' . floatval($this->price->price) . '%';
                }

                return '+ ' . price($this->price->price);
            },
        )->shouldCache();
    }

    public function prices()
    {
        return $this->hasMany(config('rapidez.models.product_option_type_price'), 'option_type_id');
    }
}
