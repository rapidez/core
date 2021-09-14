<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class CheckoutTest extends DuskTestCase
{
    public function testCheckoutAsGuest($createAccountWithEmail = false)
    {
        $this->browse(function (Browser $browser) use ($createAccountWithEmail) {
            $browser->visit($this->testProduct->url)
                ->waitUntilAllAjaxCallsAreFinished()
                ->click('@add-to-cart')
                ->waitFor('@minicart-qty')
                ->waitUntilAllAjaxCallsAreFinished()
                ->visit('/checkout')
                ->waitUntilAllAjaxCallsAreFinished()
                ->type('@email', $createAccountWithEmail ?: 'wayne@enterprises.com')
                ->click('@continue')
                ->waitUntilAllAjaxCallsAreFinished()
                ->pause(1000)
                ->waitFor('@shipping_country', 10)
                ->type('@shipping_firstname', 'Bruce')
                ->type('@shipping_lastname', 'Wayne')
                ->type('@shipping_postcode', '72000')
                ->type('@shipping_housenumber', '1007')
                ->type('@shipping_street', 'Mountain Drive')
                ->type('@shipping_city', 'Gotham')
                ->select('@shipping_country', 'TR')
                ->type('@shipping_telephone', '530-7972');

            if ($createAccountWithEmail) {
                $browser->click('@create_account')
                    ->type('@password', 'IronManSucks.91939')
                    ->type('@password_repeat', 'IronManSucks.91939');
            }

            $browser->click('@continue')
                ->waitUntilAllAjaxCallsAreFinished()
                ->click('@method-0')
                ->waitUntilAllAjaxCallsAreFinished()
                ->click('@continue')
                ->waitUntilAllAjaxCallsAreFinished()
                ->assertSee('Partytime!');
        });
    }

    public function testCheckoutAsUser()
    {
        $email = 'wayne'.mt_rand().'@enterprises.com';
        $this->testCheckoutAsGuest($email);

        $this->browse(function (Browser $browser) use ($email) {
            $browser->visit('/')
                ->click('@account_menu')
                ->click('@logout')
                ->visit($this->testProduct->url)
                ->waitUntilAllAjaxCallsAreFinished()
                ->click('@add-to-cart')
                ->waitUntilAllAjaxCallsAreFinished()
                ->visit('/checkout')
                ->waitUntilAllAjaxCallsAreFinished()
                ->type('@email', $email)
                ->click('@continue')
                ->waitUntilAllAjaxCallsAreFinished()
                ->pause(500)
                ->type('@password', 'IronManSucks.91939')
                ->click('@continue')
                ->waitUntilAllAjaxCallsAreFinished()
                ->click('@continue')
                ->waitUntilAllAjaxCallsAreFinished()
                ->click('@method-0')
                ->waitUntilAllAjaxCallsAreFinished()
                ->click('@continue')
                ->waitUntilAllAjaxCallsAreFinished()
                ->assertSee('Partytime!');
        });
    }
}
