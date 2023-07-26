<?php

namespace Rapidez\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Tierprices implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        $values = collect(json_decode($value));
        $tierprices = [];

        foreach ($values as $item) {
            $tierprices[$item->group][$item->qty] = [
                'value' => $item->value,
                'percentage' => $item->percentage,
            ];
        }

        return $tierprices;
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
