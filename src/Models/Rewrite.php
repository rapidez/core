<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;

class Rewrite extends Model
{
    protected $table = 'url_rewrite';

    protected $primaryKey = 'url_rewrite_id';

    protected static function booted()
    {
        static::addGlobalScope('store', function (Builder $builder) {
            $builder->where('store_id', config('rapidez.store'));
        });
    }
}
