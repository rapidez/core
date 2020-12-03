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
                    ->type('@firstname', 'Roy')
                    ->type('@lastname', 'Duineveld')
                    ->type('@zipcode', '1823CW')
                    ->type('@housenumber', '7')
                    ->type('@street', 'Pettemerstraat')
                    ->type('@city', 'Alkmaar')
                    ->type('@telephone', '0727100094')
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
