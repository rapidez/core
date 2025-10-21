<?php

namespace Rapidez\Core\Models\AttributeModels;

class ArrayBackend implements AttributeModel
{
    public static function value($value, $attribute)
    {
        if (! $value) {
            return collect();
        }

        return collect(explode(',', $value));
    }
}
