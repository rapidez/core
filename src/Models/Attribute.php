<?php

namespace Rapidez\Core\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Models\Scopes\Attribute\OnlyProductAttributesScope;

class Attribute extends Model
{
    protected $table = 'eav_attribute';

    protected $primaryKey = 'attribute_id';

    protected static function booting()
    {
        static::addGlobalScope(new OnlyProductAttributesScope);
    }

    public static function getCachedWhere(callable $callback): array
    {
        if (! $attributes = config('cache.app.attributes.' . config('rapidez.store'))) {
            $attributes = Cache::rememberForever('attributes.' . config('rapidez.store'), function () {
                return self::all()->toArray();
            });
            config(['cache.app.attributes.' . config('rapidez.store') => $attributes]);
        }

        return Arr::where($attributes, function ($attribute) use ($callback) {
            return $callback($attribute);
        });
    }
}
