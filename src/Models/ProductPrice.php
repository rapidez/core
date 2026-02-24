<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    public $timestamps = false;

    protected $table = 'catalog_product_index_price';

    protected $primaryKey = null;

    protected $hidden = [
        'entity_id',
        'website_id',
        'tax_class_id',
    ];

    protected $casts = [
        'price'       => 'float',
        'final_price' => 'float',
        'min_price'   => 'float',
        'max_price'   => 'float',
        'tier_price'  => 'float',
    ];

    protected static function booting()
    {
        // This index table does always have
        // values for all websites, so
        // no fallback is needed.
        static::addGlobalScope('website', function (Builder $builder) {
            $builder->where('website_id', config('rapidez.website'));
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            config('rapidez.models.product'),
            'entity_id'
        );
    }
}
