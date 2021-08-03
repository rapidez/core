<?php

namespace Rapidez\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Multiselect implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        $optionvalueModel = config('rapidez.models.optionvalue');
        if ($value) {
            foreach (explode(',', $value) as $optionValueId) {
                $values[] = $optionvalueModel::getCachedByOptionId($optionValueId);
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
