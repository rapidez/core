<?php

namespace Rapidez\Core\Tests;

use Illuminate\Support\Facades\App;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Assert;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Product;

trait DuskTestCaseSetup
{
    public string $flat;

    public Product $testProduct;

    protected function setUp(): void
    {
        parent::setUp();

        App::setLocale(strtok(Rapidez::config('general/locale/code', 'en_US'), '_'));

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
            $this->waitUntilTrueForDuration('window.app?.$data?.loading !== true && await new Promise((resolve, reject) => window.requestIdleCallback((deadline) => resolve(!deadline.didTimeout), {timeout: 5}))', $timeout); // @phpstan-ignore-line

            return $this;
        });

        Browser::macro('waitUntilVueLoaded', function () {
            /** @var Browser $this */
            $this
                ->waitUntilIdle()
                ->waitUntilTrueForDuration('document.body.contains(window.app?.$el) && window.app?._isMounted', 60, 2)
                ->waitUntilIdle();

            return $this;
        });

        Browser::macro('assertFormValid', function ($selector) {
            /** @var Browser $this */
            $fullSelector = $this->resolver->format($selector);
            Assert::assertEquals(
                true,
                $this->driver->executeScript("return document.querySelector('{$fullSelector}').reportValidity();"),
                'Form is not valid: ' . PHP_EOL . $this->driver->executeScript("return Array.from(document.querySelector('{$fullSelector}').elements).filter(el => !el.validity.valid).map(el => el.name + ': ' + el.validationMessage).join('\\n');")
            );

            return $this;
        });

        Browser::macro('addProductToCart', function ($productUrl = null) {
            /** @var Browser $this */
            if ($productUrl) {
                $this
                    ->visit($productUrl)
                    ->waitUntilVueLoaded()
                    ->waitUntilIdle();
            }

            $this
                ->waitUntilIdle()
                ->waitUntilEnabled('@add-to-cart', 200)
                ->press('@add-to-cart', 120)
                ->waitForText(__('Added'), 120)
                ->waitUntilIdle();

            return $this;
        });

        $this->flat = (new Product)->getTable();

        $this->testProduct = Product::selectAttributes([
            'name',
            'price',
            'url_key',
        ])->firstWhere(
            $this->flat . '.sku',
            config('testing.product', '24-WB02')
        );
    }
}
