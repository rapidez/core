<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class CartTest extends DuskTestCase
{
    /**
     * @test
     */
    public function addSimpleProduct(): void
    {
        $this->browse(function (Browser $browser): void {
            $this->addProduct($browser, $this->testProduct->url ?? '/') // @phpstan-ignore-line
                ->visit('/cart')
                ->waitUntilIdle()
                ->waitFor('@cart-content', 15)
                ->waitUntilIdle()
                ->assertSee($this->testProduct->name ?? '');
        });
    }

    /**
     * @test
     */
    public function addMultipleSimpleProduct(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->script('localStorage.clear();');
            $this->addProduct($browser, $this->testProduct->url ?? '/');
            $this->addProduct($browser, $this->testProduct->url ?? '/');
            $browser->assertSeeIn('@minicart-count', '2');
        });
    }

    /**
     * @test
     */
    public function changeProductQty(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/cart') // @phpstan-ignore-line
                ->waitUntilIdle()
                ->waitFor('@cart-content', 15)
                ->waitUntilIdle()
                ->type('@qty-0', 5)
                ->pressAndWaitFor('@item-update-0')
                ->waitUntilIdle()
                ->assertSee(($this->testProduct->price ?? 0) * 5);
        });
    }

    /**
     * @test
     */
    public function removeProduct(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/cart') // @phpstan-ignore-line
                ->waitUntilIdle()
                ->waitFor('@cart-content', 15)
                ->waitUntilIdle()
                ->click('@item-delete-0')
                ->waitUntilIdle()
                ->assertDontSee('@cart-item-name');
        });
    }

    public function addProduct(Browser $browser, string $url): Browser
    {
        return $browser->visit($url) // @phpstan-ignore-line
            ->waitUntilIdle()
            ->pressAndWaitFor('@add-to-cart', 60)
            ->waitForText('Added', 60);
    }
}
