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
                    ->type('@email', 'guest@test.nl')
                    ->click('@continue')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->pause(1000)
                    ->type('@shipping_firstname', 'Roy')
                    ->type('@shipping_lastname', 'Duineveld')
                    ->type('@shipping_zipcode', '1823CW')
                    ->type('@shipping_housenumber', '7')
                    ->type('@shipping_street', 'Pettemerstraat')
                    ->type('@shipping_city', 'Alkmaar')
                    ->type('@shipping_telephone', '0727100094')
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
