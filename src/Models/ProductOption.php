<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductOption extends Model
{
    protected $table = 'catalog_product_option';

    protected $primaryKey = 'option_id';

    public $timestamps = false;

    protected $with = ['values', 'titles', 'prices'];

    protected $appends = ['title', 'price', 'price_label'];

    protected $hidden = ['titles', 'prices'];

    protected $casts = [
        'is_require' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(config('rapidez.models.product'), 'product_id');
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->titles->firstForCurrentStore()->title,
        )->shouldCache();
    }

    public function titles()
    {
        return $this->hasMany(config('rapidez.models.product_option_title'), 'option_id');
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
                if (! floatval($this->price?->price)) {
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
        return $this->hasMany(config('rapidez.models.product_option_price'), 'option_id');
    }

    public function values()
    {
        return $this->hasMany(config('rapidez.models.product_option_type_value'), 'option_id');
    }
}
