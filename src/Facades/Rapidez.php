<?php

namespace Rapidez\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self addFallbackRoute($action, $position = 9999)
 * @method static self removeFallbackRoute($action)
 * @method static Collection getAllFallbackRoutes()
 * @method static ?string config(string $path, $default = null, bool $sensitive = false)
 * @method static ?string content($content)
 * @method static object fancyMagentoSyntaxDecoder(string $encodedString)
 * @method static array getStores($storeId = null)
 * @method static array getStore($storeId)
 * @method static void setStore($store)
 *
 * @see \Rapidez\Core\Rapidez
 */
class Rapidez extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rapidez';
    }
}
