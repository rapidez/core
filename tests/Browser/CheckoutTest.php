<?php

namespace Rapidez\Core\Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class CheckoutTest extends DuskTestCase
{
    public function testCheckoutAsGuest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit($this->testProduct->url)
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->click('@add-to-cart')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->visit('/checkout')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->type('@email', 'wayne@enterprises.com')
                    ->click('@continue')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->pause(1000)
                    ->type('@shipping_firstname', 'Bruce')
                    ->type('@shipping_lastname', 'Wayne')
                    ->type('@shipping_postcode', '72000')
                    ->type('@shipping_housenumber', '1007')
                    ->type('@shipping_street', 'Mountain Drive')
                    ->type('@shipping_city', 'Gotham')
                    ->type('@shipping_country', 'TR')
                    ->type('@shipping_telephone', '530-7972')
                    ->click('@continue')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->click('@method-0')
                    ->click('@continue')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->assertSee('Partytime!');
        });
    }

    public function testCheckoutAsUser()
    {
        $this->markTestSkipped('TODO: We need to create a user.');

        $this->browse(function (Browser $browser) {
            $browser->visit($this->testProduct->url)
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->click('@add-to-cart')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->visit('/checkout')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->type('@email', 'user@test.nl')
                    ->click('@continue')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->type('@password', 'password')
                    ->click('@continue')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->click('@continue')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->click('@method-0')
                    ->click('@continue')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->assertSee('Partytime!');
        });
    }
}
