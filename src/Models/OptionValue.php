<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Model;
use Illuminate\Support\Facades\Cache;

class OptionValue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'eav_attribute_option_value';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'value_id';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array',
    ];

    public static function getCachedByOptionId(int $optionId): string
    {
        if (!$optionValues = config('cache.app.optionvalues')) {
            $optionValues = Cache::rememberForever('optionvalues', function () {
                return self::select('option_id')
                    ->selectRaw('JSON_OBJECTAGG(store_id, `value`) as `value`')
                    ->groupBy('option_id')
                    ->get()
                    ->keyBy('option_id')
                    ->toArray();
            });

            config(['cache.app.optionvalues' => $optionValues]);
        }

        $optionValue = $optionValues[$optionId]['value'];
        return html_entity_decode($optionValue[config('rapidez.store')] ?? $optionValue[0]);
    }
}
