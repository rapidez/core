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

        Browser::macro('waitUntilTrueForDuration', function (string $script = 'true', $timeout = 120, $for = 0.5) {
            // Waits until the script is truthy for x seconds, supports await.
            $interval = 0.05;

            /** @var Browser $this */
            $this->waitUntil('await new Promise((resolve, reject) => {
                let counter = 0;
                let interval = setInterval(async function () {
                    let result = ' . $script . ';
                    counter = result ? counter + 1 : 0;
                    if (counter >= ' . ($for / $interval) . ') {
                        clearInterval(interval)
                        resolve(true)
                    }
                }, ' . ($interval * 1000) . ');
                setTimeout(() => resolve(false), ' . ($timeout * 1000) . ')
            }) === true', $timeout);

            return $this;
        });

        Browser::macro('waitUntilIdle', function ($timeout = 120) {
            /** @var Browser $this */
            $this->waitUntilTrueForDuration('window.app?.$data?.loading !== true && await new Promise((resolve, reject) => window.requestIdleCallback((deadline) => resolve(!deadline.didTimeout), {timeout: 5}))', $timeout);

            return $this;
        });

        Browser::macro('assertFormValid', function ($selector) {
            /** @var Browser $this */
            $fullSelector = $this->resolver->format($selector);
            $this->assertScript('document.querySelector("' . $fullSelector . '").reportValidity();');

            return $this;
        });

        $this->flat = (new Product)->getTable();

        $this->testProduct = Product::selectAttributes([
            'name',
            'price',
            'url_key',
        ])->firstWhere($this->flat . '.sku', env('TEST_PRODUCT', '24-WB02'));
    }
}
