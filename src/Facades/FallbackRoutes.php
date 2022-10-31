<?php

namespace Rapidez\Core\Facades;

use Illuminate\Support\Facades\Facade;
use Rapidez\Core\FallbackRoutesRepository;

/**
 * @method static self add($action, $position = 99999)
 * @method static Collection all()
 *
 * @see \Rapidez\Core\FallbackRoutesRepository
 */
class FallbackRoutes extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FallbackRoutesRepository::class;
    }
}
