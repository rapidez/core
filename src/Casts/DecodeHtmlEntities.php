<?php

namespace Rapidez\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/** @implements CastsAttributes<string|null, string|null> */
class DecodeHtmlEntities implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return html_entity_decode($value);
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
