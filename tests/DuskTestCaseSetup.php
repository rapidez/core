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

        Browser::macro('waitUntilTrueForDuration', function (string $expression = 'true', float $milliseconds = 500, int $intervalMs = 50, $timeoutSeconds = 120) {
            // Waits until the expression is truthy for x milliseconds, supports await.
            /** @var Browser $this */

            $this->waitUntil('await new Promise((resolve, reject) => {
                let counter = 0;
                let interval = setInterval(async function () {
                    let result = '.$expression.';
                    counter = result ? counter + 1 : 0;
                    if (counter >= '.($milliseconds / $intervalMs).') {
                        clearInterval(interval)
                        resolve(true)
                    }
                }, '.$intervalMs.');
                setTimeout(() => resolve(false), '.($timeoutSeconds * 1000).')
            }) === true', $timeoutSeconds);

            return $this;
        });

        Browser::macro('waitUntilAllAjaxCallsAreFinished', function ($pauseMs = false) {
            /** @var Browser $this */
            $this->waitUntilTrueForDuration('window.app.$data?.loading === false && await new Promise((resolve, reject) => window.requestIdleCallback((deadline) => resolve(!deadline.didTimeout), {timeout: 2}))', $pauseMs ?: 500);

            return $this;
        });

        Browser::macro('waitUntilIdle', function ($seconds = null) {
            /** @var Browser $this */
            $this->waitUntilTrueForDuration('await new Promise((resolve, reject) => window.requestIdleCallback((deadline) => resolve(!deadline.didTimeout), {timeout: 2}))', null, null, $seconds ?: 120);

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
