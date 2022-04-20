<?php

namespace Rapidez\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Rapidez extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rapidez';
    }
}
