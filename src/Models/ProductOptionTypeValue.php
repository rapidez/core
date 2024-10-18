<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOptionTypeValue extends Model
{
    protected $table = 'catalog_product_option_type_value';

    protected $primaryKey = 'option_type_id';

    public $timestamps = false;

    protected $with = ['titles', 'prices'];

    protected $appends = ['title', 'price', 'price_label'];

    protected $hidden = ['titles', 'prices'];

    /** @return BelongsTo<ProductOption, ProductOptionTypeValue> */
    public function option(): BelongsTo
    {
        return $this->belongsTo(config('rapidez.models.product_option'), 'option_id');
    }

    /** @return Attribute<string, null> */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->titles->firstForCurrentStore()->title, // @phpstan-ignore-line
        )->shouldCache();
    }

    /** @return HasMany<ProductOptionTypeTitle, ProductOptionTypeValue> */
    public function titles(): HasMany
    {
        return $this->hasMany(config('rapidez.models.product_option_type_title'), 'option_type_id');
    }

    /** @return Attribute<ProductOptionTypePrice|null, null> */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->prices->firstForCurrentStore(), // @phpstan-ignore-line
        )->shouldCache();
    }

    /** @return Attribute<string|null, null> */
    protected function priceLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->price || ! floatval($this->price->price)) {
                    return;
                }

                if ($this->price->price_type == 'percent') {
                    return '+ ' . floatval($this->price->price) . '%';
                }

                return '+ ' . price($this->price->price);
            },
        )->shouldCache();
    }

    /** @return HasMany<ProductOptionTypePrice, ProductOptionTypeValue> */
    public function prices(): HasMany
    {
        return $this->hasMany(config('rapidez.models.product_option_type_price'), 'option_type_id');
    }
}
