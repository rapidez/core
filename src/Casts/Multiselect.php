<?php

namespace Rapidez\Core\Casts;

use Rapidez\Core\Models\OptionValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Multiselect implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        if ($value) {
            foreach (explode(',', $value) as $optionValueId) {
                $values[] = OptionValue::getCachedByOptionId($optionValueId);
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
