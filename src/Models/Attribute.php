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
        parent::booted();
        static::addGlobalScope(new OnlyProductAttributesScope());
    }

    public static function getCachedWhere(callable $callback): array
    {
        if (!$attributes = config('cache.app.attributes')) {
            $attributes = Cache::rememberForever('attributes', function () {
                return self::all()->toArray();
            });
            config(['cache.app.attributes' => $attributes]);
        }

        return Arr::where(self::all()->toArray(), function ($attribute) use ($callback) {
            return $callback($attribute);
        });
    }
}
