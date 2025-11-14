<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    public $timestamps = false;

    protected $table = 'catalog_product_index_price';

    protected $primaryKey = null;

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
