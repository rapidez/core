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
                ->waitUntilEnabled('@add-to-cart')
                ->click('@add-to-cart')
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitUntil('localStorage.mask')
                ->waitUntilAllAjaxCallsAreFinished()
                ->visit('/checkout')
                ->waitUntilAllAjaxCallsAreFinished()
                ->type('@email', $createAccountWithEmail ?: 'wayne@enterprises.com')
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitUntilEnabled('@continue')
                ->click('@continue')
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitFor('@shipping_country', 15)
                ->type('@shipping_firstname', 'Bruce')
                ->type('@shipping_lastname', 'Wayne')
                ->type('@shipping_postcode', '72000')
                ->type('@shipping_housenumber', '1007')
                ->type('@shipping_street', 'Mountain Drive')
                ->type('@shipping_city', 'Gotham')
                ->select('@shipping_country', 'NL')
                ->type('@shipping_telephone', '530-7972');

            if ($createAccountWithEmail) {
                $browser->click('@create_account')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->type('@password', 'IronManSucks.91939')
                    ->type('@password_repeat', 'IronManSucks.91939');
            }

            $browser
                ->waitUntilAllAjaxCallsAreFinished()
                ->click('@method-0') // select shipping method
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitUntilEnabled('@continue')
                ->click('@continue') // go to payment step
                ->waitUntilAllAjaxCallsAreFinished(2000)
                ->waitForText('Payment method')
                ->click('@method-0') // select payment method
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitUntilEnabled('@continue')
                ->click('@continue') // place order
                ->waitUntilAllAjaxCallsAreFinished()
                ->assertPresent('@checkout-success');
        });
    }

    public function testCheckoutAsUser()
    {
        $email = 'wayne'.mt_rand().'@enterprises.com';
        $this->testCheckoutAsGuest($email);

        $this->browse(function (Browser $browser) use ($email) {
            $browser->waitForReload(fn ($browser) => $browser->visit('/'), 4)
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitFor('@account_menu')
                ->click('@account_menu')
                ->click('@logout')
                ->visit($this->testProduct->url)
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitUntilEnabled('@add-to-cart')
                ->click('@add-to-cart')
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitUntil('localStorage.mask')
                ->waitUntilAllAjaxCallsAreFinished()
                ->visit('/checkout')
                ->waitUntilAllAjaxCallsAreFinished()
                ->type('@email', $email)
                ->waitUntilEnabled('@continue')
                ->click('@continue')
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitFor('@password')
                ->type('@password', 'IronManSucks.91939')
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitUntilEnabled('@continue')
                ->click('@continue') // login
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitForText('Payment method')
                ->click('@method-0') // select shipping method
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitUntilEnabled('@continue')
                ->click('@continue') // go to payment step
                ->waitUntilAllAjaxCallsAreFinished()
                ->click('@method-0') // select payment method
                ->waitUntilAllAjaxCallsAreFinished()
                ->waitUntilEnabled('@continue')
                ->click('@continue') // place order
                ->waitUntilAllAjaxCallsAreFinished()
                ->assertPresent('@checkout-success');
        });
    }
}
