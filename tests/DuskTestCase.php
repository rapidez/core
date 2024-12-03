<?php

namespace Rapidez\Core\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;
use Rapidez\BladeDirectives\BladeDirectivesServiceProvider;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\RapidezServiceProvider;
use Rapidez\LaravelMultiCache\MultiStoreServiceProvider;
use TorMorten\Eventy\EventServiceProvider;

abstract class DuskTestCase extends BaseTestCase
{
    use DuskTestCaseSetup;

    protected function getPackageProviders($app)
    {
        return [
            EventServiceProvider::class,
            MultiStoreServiceProvider::class,
            RapidezServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeDirectivesServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Rapidez' => Rapidez::class,
        ];
    }
}
