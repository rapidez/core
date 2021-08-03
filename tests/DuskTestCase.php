<?php

namespace Rapidez\Core\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Laravel\Dusk\Browser;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;
use Rapidez\Core\Models\Product;
use Rapidez\Core\RapidezFacade;
use Rapidez\Core\RapidezServiceProvider;
use TorMorten\Eventy\EventServiceProvider;

abstract class DuskTestCase extends BaseTestCase
{
    public string $flat;

    public Product $testProduct;

    protected function setUp(): void
    {
        parent::setUp();

        Browser::macro('waitUntilAllAjaxCallsAreFinished', function () {
            $this->waitUntil('!window.app.$data.loading', 10);

            return $this;
        });

        $this->flat = (new Product())->getTable();

        $this->testProduct = Product::selectAttributes([
            'name',
            'price',
            'url_key',
        ])->firstWhere($this->flat.'.sku', '24-WB02');
    }

    protected function getPackageProviders($app)
    {
        return [
            EventServiceProvider::class,
            RapidezServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Rapidez' => RapidezFacade::class,
        ];
    }
}
