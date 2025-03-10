<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
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
        // TODO: Double check; this config has been (re)moved.
        return CastsAttribute::make(
            get: fn ($value) => $value || []
            // in_array($this->code, config('rapidez.indexer.additional_filters')),
        )->shouldCache();
    }

    protected function prefix(): CastsAttribute
    {
        return CastsAttribute::make(
            get: function() {
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

    public static function getCachedWhere(callable $callback): array
    {
        $attributes = Cache::store('rapidez:multi')->rememberForever('attributes.' . config('rapidez.store'), function () {
            return self::all()->toArray();
        });

        return Arr::where($attributes, function ($attribute) use ($callback) {
            return $callback($attribute);
        });
    }
}
