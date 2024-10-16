<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rewrite extends Model
{
    protected $table = 'url_rewrite';

    protected $primaryKey = 'url_rewrite_id';

    protected static function booting()
    {
        static::addGlobalScope('store', function (Builder $builder) {
            $builder->where('store_id', config('rapidez.store'));
        });
    }

    /** @return HasOne<Store> */
    public function store(): HasOne
    {
        // @phpstan-ignore-next-line
        return $this->hasOne(
            config('rapidez.models.store'),
            'store_id',
            'store_id',
        );
    }
}
