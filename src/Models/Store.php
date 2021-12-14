<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Exceptions\StoreNotFoundException;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Store extends Model
{
    protected $table = 'store';

    protected $primaryKey = 'store_id';

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope());
        static::addGlobalScope('defaults', function (Builder $builder) {
            $builder
                ->where('store.code', '<>', 'admin')
                ->join('store_group', 'store_group.group_id', '=', 'store.group_id');
        });
    }

    public static function getCachedWhere(callable $callback): array
    {
        if (!$stores = config('cache.app.stores')) {
            $stores = Cache::rememberForever('stores', function () {
                return self::select([
                    'store_id',
                    'store.code',
                    'store.website_id',
                    'root_category_id',
                ])->get()->keyBy('store_id')->toArray();
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
