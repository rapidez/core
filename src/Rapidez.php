<?php

namespace Rapidez\Core;

use Rapidez\Core\Models\Config;

class Rapidez
{
    public function config(string $path, $default = null): ?string
    {
        return Config::getCachedByPath($path, $default);
    }
}
