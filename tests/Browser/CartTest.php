<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class CartTest extends DuskTestCase
{
    /**
     * @test
     */
    public function addSimpleProduct()
    {
        $this->browse(function (Browser $browser) {
            $browser->addProductToCart($this->testProduct->url)
                ->visit('/cart')
                ->waitUntilIdle()
                ->waitFor('@cart-content', 15)
                ->waitUntilIdle()
                ->assertSee($this->testProduct->name);
        });
    }

    /**
     * @test
     */
    public function addMultipleSimpleProduct()
    {
        $this->browse(function (Browser $browser) {
            $browser->deleteCookie('mask');
            $browser->script('localStorage.clear();');
            $browser->addProductToCart($this->testProduct->url);
            $browser->addProductToCart($this->testProduct->url);
            $browser->assertSeeIn('@minicart-count', 2);
        });
    }

    /**
     * @test
     */
    public function changeProductQty()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/cart')
                ->waitUntilIdle()
                ->waitFor('@cart-content', 15)
                ->waitUntilIdle()
                ->type('@qty-0', 5)
                ->pressAndWaitFor('@item-update-0')
                ->waitUntilIdle()
                ->assertSee($this->testProduct->price * 5);
        });
    }

    /**
     * @test
     */
    public function removeProduct()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/cart')
                ->waitUntilIdle()
                ->waitFor('@cart-content', 15)
                ->waitUntilIdle()
                ->click('@item-delete-0')
                ->waitUntilIdle()
                ->assertDontSee('@cart-item-name');
        });
    }
}
