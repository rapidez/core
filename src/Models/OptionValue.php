<?php

namespace Rapidez\Core\Models;

use Illuminate\Support\Facades\Cache;

class OptionValue extends Model
{
    protected $table = 'eav_attribute_option_value';

    protected $primaryKey = 'value_id';

    public static function getCachedByOptionId(int $optionId, ?int $attributeId = null, mixed $default = false): string
    {
        $cacheKey = 'optionvalues.' . config('rapidez.store');
        $cache = Cache::store('rapidez:multi')->tags('attributes')->get($cacheKey, []);

        if (! isset($cache[$optionId])) {
            $cache[$optionId] = html_entity_decode(self::where('eav_attribute_option_value.option_id', $optionId)
                ->whereIn('store_id', [config('rapidez.store'), 0])
                ->join('eav_attribute_option', 'eav_attribute_option.option_id', '=', 'eav_attribute_option_value.option_id')
                ->orderByDesc('store_id')
                ->when($attributeId, fn ($query) => $query->where('attribute_id', $attributeId))
                ->first('value')
                ->value ?? $default);
            Cache::store('rapidez:multi')->tags('attributes')->forever($cacheKey, $cache);
        }

        return $cache[$optionId];
    }
}
