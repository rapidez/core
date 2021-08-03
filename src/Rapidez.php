<?php

namespace Rapidez\Core;

use Rapidez\Core\Models\Config;

class Rapidez
{
    public function config(string $path, $default = null): ?string
    {
        $configModel = config('rapidez.models.config');
        return $configModel::getCachedByPath($path, $default);
    }
}
