<?php

namespace Rapidez\Core\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Models\Scopes\Attribute\OnlyProductAttributesScope;

class Attribute extends Model
{
    protected $table = 'eav_attribute';

    protected $primaryKey = 'attribute_id';

    protected static function booted()
    {
        static::addGlobalScope(new OnlyProductAttributesScope());
        $scopes = Eventy::filter('attribute.scopes', []);
        foreach ($scopes as $scope) {
            static::addGlobalScope(new $scope());
        }
    }

    public static function getCachedWhere(callable $callback): array
    {
        if (!$attributes = config('cache.app.attributes')) {
            $attributes = Cache::rememberForever('attributes', function () {
                return self::all()->toArray();
            });
            config(['cache.app.attributes' => $attributes]);
        }

        return Arr::where($attributes, function ($attribute) use ($callback) {
            return $callback($attribute);
        });
    }
}
