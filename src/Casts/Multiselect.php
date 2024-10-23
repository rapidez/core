<?php

namespace Rapidez\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/** @implements CastsAttributes<array<int, \Rapidez\Core\Models\OptionValue>|float|int|string|false|null, string|null> */
class Multiselect implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        $optionvalueModel = config('rapidez.models.option_value');
        if ($value) {
            foreach (explode(',', $value) as $optionValueId) {
                $values[] = is_numeric($optionValueId)
                    ? $optionvalueModel::getCachedByOptionId($optionValueId)
                    : $optionValueId;
            }

            return $values;
        }

        return $value;
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
