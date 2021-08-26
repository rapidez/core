<?php

namespace Rapidez\Core\Models;

use Illuminate\Support\Facades\Cache;

class OptionValue extends Model
{
    protected $table = 'eav_attribute_option_value';

    protected $primaryKey = 'value_id';

    public static function getCachedByOptionId(int $optionId): string
    {
        $cacheKey = 'optionvalue.'.config('rapidez.store').'.'.$optionId;

        if (!$optionValue = config('cache.app.'.$cacheKey)) {
            $optionValue = Cache::rememberForever($cacheKey, function () use ($optionId) {
                return html_entity_decode(self::where('option_id', $optionId)
                    ->whereIn('store_id', [config('rapidez.store'), 0])
                    ->orderByDesc('store_id')
                    ->first('value')
                    ->value);
            });

            config(['cache.app.'.$cacheKey => $optionValue]);
        }

        return $optionValue;
    }
}
