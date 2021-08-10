<?php

namespace Rapidez\Core;

use Rapidez\Core\Models\Traits\HasContentAttributeWithVariables;
use Illuminate\Support\Str;

class Rapidez
{
    public function config(string $path, $default = null): ?string
    {
        return config('rapidez.models.config')::getCachedByPath($path, $default);
    }

    public function getContent($content)
    {
        return $this->getContentAttribute($content);
    }
}
