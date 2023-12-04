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
            $this->addProduct($browser, $this->testProduct->url)
                ->visit('/cart')
                ->assertSee($this->testProduct->name);
        });
    }

    /**
     * @test
     */
    public function addMultipleSimpleProduct()
    {
        $this->browse(function (Browser $browser) {
            $browser->script('localStorage.clear();');
            $this->addProduct($browser, $this->testProduct->url);
            $this->addProduct($browser, $this->testProduct->url);
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
                ->type('@qty-0', 5)
                ->click('@item-update-0')
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
                ->click('@item-delete-0')
                ->waitUntilIdle()
                ->assertDontSee('@cart-item-name');
        });
    }

    public function addProduct($browser, $url)
    {
        return $browser->visit($url)
            ->waitUntilIdle()
            ->click('@add-to-cart')
            ->waitForText('Added', 60);
    }
}
