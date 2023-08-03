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

    public function store(): HasOne
    {
        return $this->hasOne(
            Store::class,
            'store_id',
            'store_id',
        );
    }
}
