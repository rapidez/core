<?php

namespace Rapidez\Core\Models\AttributeModels;

class ArrayBackend implements AttributeModel
{
    public static function value($value, $attribute)
    {
        $values = explode(',', $value);

        return collect($values)->map(fn ($value) => $attribute->options[$value]?->value ?? $value);
    }
}
