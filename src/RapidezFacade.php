<?php

namespace Rapidez\Core;

use Illuminate\Support\Facades\Facade;

class RapidezFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rapidez';
    }
}
