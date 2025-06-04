<?php

namespace Rapidez\Core\Models;

use Illuminate\Support\Facades\Cache;

class OptionValue extends Model
{
    protected $table = 'eav_attribute_option_value';

    protected $primaryKey = 'value_id';

    public static function getCachedByOptionId(int $optionId): string
    {
        $cacheKey = 'optionvalues.' . config('rapidez.store');
        $cache = Cache::memo()->get($cacheKey, []);

        if (! isset($cache[$optionId])) {
            $cache[$optionId] = html_entity_decode(self::where('option_id', $optionId)
                ->whereIn('store_id', [config('rapidez.store'), 0])
                ->orderByDesc('store_id')
                ->first('value')
                ->value ?? false);
            Cache::memo()->forever($cacheKey, $cache);
        }

        return $cache[$optionId];
    }
}
