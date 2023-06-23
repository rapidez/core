<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductOptionTypeValue extends Model
{
    public $timestamps = false;

    protected $table = 'catalog_product_option_type_value';

    protected $primaryKey = 'option_type_id';

    protected $appends = ['title', 'price', 'price_label'];

    protected $with = ['titles', 'prices'];

    public function option()
    {
        return $this->belongsTo(config('rapidez.models.product_option'), 'option_id');
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            get: function () {
                $titles = $this->titles->pluck('title', 'store_id')->toArray();
                return $titles[config('rapidez.store')] ?? $titles[0];
            },
        )->shouldCache();
    }

    public function titles()
    {
        return $this->hasMany(config('rapidez.models.product_option_type_title'), 'option_type_id');
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->prices->sortByDesc('store_id')->first(),
        )->shouldCache();
    }

    protected function priceLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!floatval($this->price->price)) {
                    return;
                }

                if ($this->price->price_type == 'percent') {
                    return '+ '.floatval($this->price->price).'%';
                }

                return '+ '.price($this->price->price);
            },
        )->shouldCache();
    }

    public function prices()
    {
        return $this->hasMany(config('rapidez.models.product_option_type_price'), 'option_type_id');
    }
}
