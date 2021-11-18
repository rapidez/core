<?php

namespace Rapidez\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Children implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        $children = json_decode($value);

        foreach ($children as $child) {
            if ($child->special_price) {
                if ($child->special_from_date && $child->special_from_date > now()->toDateString()) {
                    $child->special_price = null;
                }

                if ($child->special_to_date && $child->special_to_date < now()->toDateString()) {
                    $child->special_price = null;
                }
            }

            unset($child->special_from_date, $child->special_to_date);
        }

        return $children;
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
