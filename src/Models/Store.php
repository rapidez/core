<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Exceptions\StoreNotFoundException;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Store extends Model
{
    protected $table = 'store';

    protected $primaryKey = 'store_id';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IsActiveScope);
        static::addGlobalScope('without-admin-store', function (Builder $builder) {
            $builder->where('code', '<>', 'admin');
        });
    }

    public static function getCachedWhere(callable $callback): array
    {
        if (!$stores = config('cache.app.stores')) {
            $stores = Cache::rememberForever('stores', function () {
                return self::select(['store_id', 'code', 'website_id'])->get()->keyBy('store_id')->toArray();
            });
            config(['cache.app.stores' => $stores]);
        }

        $store = Arr::first($stores, function ($attribute) use ($callback) {
            return $callback($attribute);
        });

        throw_if(
            is_null($store),
            StoreNotFoundException::class,
            'Store not found.'
        );

        return $store;
    }
}
