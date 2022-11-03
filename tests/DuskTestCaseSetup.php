<?php

namespace Rapidez\Core\Tests;

use Laravel\Dusk\Browser;
use Rapidez\Core\Models\Product;

trait DuskTestCaseSetup
{
    public string $flat;

    public Product $testProduct;

    protected function setUp(): void
    {
        parent::setUp();

        Browser::macro('waitUntilAllAjaxCallsAreFinished', function ($pause = false) {
            $this->waitUntil('!window.app.$data.loading', 10);

            if ($pause) {
                $this->pause($pause);
            }

            return $this;
        });

        $this->flat = (new Product())->getTable();

        $this->testProduct = Product::selectAttributes([
            'name',
            'price',
            'url_key',
        ])->firstWhere($this->flat.'.sku', env('TEST_PRODUCT', '24-WB02'));
    }
}
