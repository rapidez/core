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

        Browser::macro('waitUntilAllAjaxCallsAreFinished', function ($pauseMs = false) {
            /** @var Browser $this */
            $this->waitUntil('window.app.$data?.loading === false && await new Promise((resolve, reject) => window.requestIdleCallback((deadline) => resolve(!deadline.didTimeout), {timeout: 2}))', 10);

            if ($pauseMs) {
                $this->pause($pauseMs);
            }

            return $this;
        });

        Browser::macro('waitUntilIdle', function ($seconds = null) {
            /** @var Browser $this */
            $this->waitUntil('await new Promise((resolve, reject) => window.requestIdleCallback((deadline) => resolve(!deadline.didTimeout), {timeout: '.($seconds ? 2 : 0).'}));', $seconds);

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
