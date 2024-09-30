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
        static::addGlobalScope(new IsActiveScope);
        static::addGlobalScope('defaults', function (Builder $builder) {
            $builder
                ->where('store.code', '<>', 'admin')
                ->join('store_group', 'store_group.group_id', '=', 'store.group_id')
                ->join('store_website', 'store_website.website_id', '=', 'store_group.website_id');
        });
    }

    public static function getCached(): array
    {
        $stores = once(fn() => Cache::rememberForever('stores', function () {
            return self::select([
                'store_id',
                'store.name',
                'store.code',
                'store.website_id',
                'store.group_id',
                'store_group.root_category_id',
                'store_website.code AS website_code',
            ])->get()->keyBy('store_id')->toArray();
        }));

        return $stores;
    }

    public static function getCachedWhere(callable $callback): array
    {
        $stores = static::getCached();

        $store = Arr::first($stores, function ($store) use ($callback) {
            return $callback($store);
        });

        throw_if(
            is_null($store),
            StoreNotFoundException::class,
            'Store not found.'
        );

        return $store;
    }
}
