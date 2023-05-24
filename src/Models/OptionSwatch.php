<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class OptionSwatch extends Model
{
    protected $table = 'eav_attribute_option_swatch';

    protected $primaryKey = 'swatch_id';

    protected $casts = [
        'options' => 'collection',
    ];

    public static function getCachedSwatchValues(): array
    {
        $attributeModel = config('rapidez.models.attribute');
        $swatchAttributes = Arr::pluck($attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['text_swatch'] || $attribute['visual_swatch'];
        }), 'id', 'code');

        return Cache::rememberForever('swatchvalues', function () use ($swatchAttributes) {
            return self::select('eav_attribute.attribute_code')
                ->selectRaw('JSON_OBJECTAGG(eav_attribute_option_value.option_id, JSON_OBJECT(
                    "label", COALESCE(eav_attribute_option_value_store.value, eav_attribute_option_value.value),
                    "sort_order", eav_attribute_option.sort_order,
                    "value", eav_attribute_option_value.option_id,
                    "swatch", eav_attribute_option_swatch.value
                )) as options')
                ->join('eav_attribute_option', function ($query) use ($swatchAttributes) {
                    $query->on('eav_attribute_option.option_id', '=', 'eav_attribute_option_swatch.option_id')
                        ->whereIn('eav_attribute_option.attribute_id', $swatchAttributes);
                })
                ->leftJoin('eav_attribute_option_value as eav_attribute_option_value_store', function ($query) use ($swatchAttributes) {
                    $query->on('eav_attribute_option.option_id', '=', 'eav_attribute_option_value_store.option_id')
                        ->whereIn('eav_attribute_option.attribute_id', $swatchAttributes)
                        ->where('eav_attribute_option_value_store.store_id', '=', config('rapidez.store'));
                })
                ->join('eav_attribute_option_value', function ($query) use ($swatchAttributes) {
                    $query->on('eav_attribute_option.option_id', '=', 'eav_attribute_option_value.option_id')
                        ->whereIn('eav_attribute_option.attribute_id', $swatchAttributes)
                        ->where('eav_attribute_option_value.store_id', '=', 0);
                })
                ->join('eav_attribute', 'eav_attribute.attribute_id', '=', 'eav_attribute_option.attribute_id')
                ->whereNotNull('eav_attribute_option_swatch.value')
                ->groupBy('eav_attribute.attribute_code')
                ->distinct()
                ->get()
                ->keyBy('attribute_code')
                ->map(function (self $optionSwatch) {
                    $optionSwatch->options = $optionSwatch->options->sortBy('sort_order')->values();

                    return $optionSwatch;
                })
                ->toArray();
        });
    }
}
