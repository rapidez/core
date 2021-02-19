<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\Attribute\OnlyProductAttributesScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Attribute extends Model
{
    protected $table = 'eav_attribute';

    protected $primaryKey = 'attribute_id';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyProductAttributesScope);
    }

    public static function allCached(): array
    {
        if (!$attributes = config('cache.app.attributes')) {
            $attributes = Cache::rememberForever('attributes', function () {
                return self::all()->keyBy('id')->toArray();
            });
            config(['cache.app.attributes' => $attributes]);
        }

        return $attributes;
    }

    public static function getCachedWhere(callable $callback): array
    {
        return Arr::where(self::allCached(), function ($attribute) use ($callback) {
            return $callback($attribute);
        });
    }

    public static function getCachedWhereFirst(callable $callback): ?array
    {
        return array_values(self::getCachedWhere($callback))[0] ?? null;
    }
}
