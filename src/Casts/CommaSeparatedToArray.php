<?php

namespace Rapidez\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CommaSeparatedToArray implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return $value ? explode(',', $value) : [];
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
