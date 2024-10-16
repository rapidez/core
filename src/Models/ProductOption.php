<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $title
 * @property ?string $type
 */
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

    /** @return BelongsTo<Product, ProductOption> */
    public function product(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.product'), 'product_id');
    }

    /** @return Attribute<string, null> */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->titles->firstForCurrentStore()->title, // @phpstan-ignore-line
        )->shouldCache();
    }

    /** @return HasMany<ProductOptionTitle> */
    public function titles(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(config('rapidez.models.product_option_title'), 'option_id');
    }

    /** @return Attribute<ProductOptionPrice|null, null> */
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

    /** @return HasMany<ProductOptionPrice> */
    public function prices(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(config('rapidez.models.product_option_price'), 'option_id');
    }

    /** @return HasMany<ProductOptionTypeValue> */
    public function values(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(config('rapidez.models.product_option_type_value'), 'option_id');
    }
}
