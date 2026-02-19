<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Rapidez\Core\Models\Scopes\Attribute\OnlyProductAttributesScope;

class Attribute extends Model
{
    protected $table = 'eav_attribute';

    protected $primaryKey = 'attribute_id';

    protected $appends = ['prefix'];

    protected static function booting()
    {
        static::addGlobalScope(new OnlyProductAttributesScope);
    }

    protected function filter(): CastsAttribute
    {
        return CastsAttribute::make(
            get: fn ($value) => $value || in_array($this->code, Arr::pluck(config('rapidez.searchkit.filter_attributes'), 'field')),
        )->shouldCache();
    }

    protected function prefix(): CastsAttribute
    {
        return CastsAttribute::make(
            get: function () {
                if ($this->super) {
                    return 'super_';
                }

                if ($this->visual_swatch) {
                    return 'visual_';
                }

                return '';
            }
        )->shouldCache();
    }

    public static function getCached()
    {
        return Cache::store('rapidez:multi')->tags('attributes')->rememberForever('eav_attributes', function () {
            return DB::table('eav_attribute')->get()->keyBy('attribute_code');
        });
    }

    public static function getCachedWhere(callable $callback): array
    {
        $attributes = Cache::store('rapidez:multi')->tags('attributes')->rememberForever('attributes.' . config('rapidez.store'), function () {
            return self::all()->toArray();
        });

        return Arr::where($attributes, function ($attribute) use ($callback) {
            return $callback($attribute);
        });
    }

    public static function hasKeyword($code)
    {
        $mapping = Product::getIndexedMapping();
        return boolval(data_get($mapping[$code] ?? [], 'fields.keyword'));
    }
}
