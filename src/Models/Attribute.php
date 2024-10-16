<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Models\Scopes\Attribute\OnlyProductAttributesScope;

/**
 * @property string $code
 */
class Attribute extends Model
{
    protected $table = 'eav_attribute';

    protected $primaryKey = 'attribute_id';

    protected static function booting()
    {
        static::addGlobalScope(new OnlyProductAttributesScope);
    }

    /** @return CastsAttribute<bool, null> */
    protected function filter(): CastsAttribute
    {
        return CastsAttribute::make(
            get: fn ($value) => $value || in_array($this->code, config('rapidez.indexer.additional_filters')),
        )->shouldCache();
    }

    /** @return array<int, Attribute> */
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
