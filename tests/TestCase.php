<?php

namespace Rapidez\Core\Tests;

use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Product;
use Rapidez\Core\RapidezServiceProvider;
use TorMorten\Eventy\EventServiceProvider;

class TestCase extends BaseTestCase
{
    public string $flat;

    public function setUp(): void
    {
        parent::setUp();

        $this->flat = (new Product())->getTable();

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            EventServiceProvider::class,
            RapidezServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Rapidez' => Rapidez::class,
        ];
    }

    protected function setUpDatabase($app)
    {
        if (Schema::hasTable('yotpo_sync')) {
            return;
        }

        fwrite(STDOUT, 'A Magento 2 database dump is being imported.'.PHP_EOL);

        exec(strtr('mysql -h HOST -P PORT -u USERNAME -pPASSWORD DATABASE < DUMP', [
            'HOST'     => config('database.connections.mysql.host'),
            'PORT'     => config('database.connections.mysql.port'),
            'USERNAME' => config('database.connections.mysql.username'),
            'PASSWORD' => config('database.connections.mysql.password'),
            'DATABASE' => config('database.connections.mysql.database'),
            'DUMP'     => __DIR__.'/database/dump.sql',
        ]));

        fwrite(STDOUT, 'Done'.PHP_EOL);
    }

    protected function getEnvironmentSetUp($app)
    {
        //
    }
}
