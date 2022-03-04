<?php

namespace Rapidez\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SortedSuperAttributes implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return collect(json_decode($value, true))
            ->mapWithKeys(function ($item, $key) {
                $item['value'] = $key;
                return [$item['sort_order'] => (object) $item];
            })
            ->sortKeys()
            ->toArray();
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
