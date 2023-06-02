<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class CartTest extends DuskTestCase
{
    public function testAddSimpleProduct()
    {
        $this->browse(function (Browser $browser) {
            $browser->plainCookie('cookie-notice', true)
                    ->visit($this->testProduct->url)
                    ->waitUntilIdle()
                    ->click('@add-to-cart')
                    ->waitForText('Added', 60)
                    ->visit('/cart')
                    ->assertSee($this->testProduct->name);
        });
    }

    public function testChangeProductQty()
    {
        $this->browse(function (Browser $browser) {
            $browser->plainCookie('cookie-notice', true)
                    ->visit('/cart')
                    ->waitUntilIdle()
                    ->type('@qty-0', 5)
                    ->click('@item-update-0')
                    ->waitUntilIdle()
                    ->assertSee($this->testProduct->price * 5);
        });
    }

    public function testRemoveProduct()
    {
        $this->browse(function (Browser $browser) {
            $browser->plainCookie('cookie-notice', true)
                    ->visit('/cart')
                    ->waitUntilIdle()
                    ->click('@item-delete-0')
                    ->waitUntilIdle()
                    ->assertDontSee('@cart-item-name');
        });
    }
}
