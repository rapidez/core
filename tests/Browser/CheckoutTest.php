<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class CheckoutTest extends DuskTestCase
{
    public function testCheckoutAsGuest()
    {
        $this->browse(function (Browser $browser) {
            $browser->script("localStorage['cookie-notice'] = true;");
            $this->addProductToCart($browser);
            $this->doCheckout($browser, 'wayne+'.mt_rand().'@enterprises.com');
        });
    }

    public function testCheckoutAsUser()
    {
        $email = 'wayne+'.mt_rand().'@enterprises.com';

        // Go through checkout as guest and register.
        $this->browse(function (Browser $browser) use ($email) {
            $browser->script("localStorage['cookie-notice'] = true;");
            $this->addProductToCart($browser);
            $this->doCheckout($browser, $email, 'IronManSucks.91939', true);
        });

        // Go through checkout as guest and log in.
        $this->browse(function (Browser $browser) use ($email) {
            $browser->script("localStorage['cookie-notice'] = true;");
            $browser->waitForReload(fn ($browser) => $browser->visit('/'), 4)
                ->waitUntilIdle()
                ->waitFor('@account_menu')
                ->click('@account_menu')
                ->click('@logout');
            $this->addProductToCart($browser);
            $this->doCheckout($browser, $email, 'IronManSucks.91939', false);
        });
    }

    public function addProductToCart($browser)
    {
        $browser->script("localStorage['cookie-notice'] = true;");
        $browser->visit($this->testProduct->url)
            ->waitUntilIdle()
            ->click('@add-to-cart')
            ->waitUntilIdle();

        return $browser;
    }

    public function doCheckout($browser, $email = false, $password = false, $register = false)
    {
        $browser
            ->visit('/checkout')
            ->waitUntilIdle()
            ->type('@email', $email ?: 'wayne@enterprises.com')
            ->click('@continue')
            ->waitUntilIdle();

        if ($password && !$register) {
            $browser
                ->type('@password', $password)
                ->waitUntilIdle()
                ->click('@continue') // login
                ->waitUntilIdle();
        } else {
            $browser
                ->waitFor('@shipping_country', 15)
                ->type('@shipping_firstname', 'Bruce')
                ->type('@shipping_lastname', 'Wayne')
                ->type('@shipping_postcode', '72000')
                ->type('@shipping_housenumber', '1007')
                ->type('@shipping_street', 'Mountain Drive')
                ->type('@shipping_city', 'Gotham')
                ->select('@shipping_country', 'NL')
                ->type('@shipping_telephone', '530-7972')
                ->waitUntilIdle();
        }

        if ($password && $register) {
            $browser->click('@create_account')
                ->waitUntilIdle()
                ->type('@password', $password)
                ->type('@password_repeat', $password)
                ->waitUntilIdle();
        }

        $browser
            ->click('@method-0') // select shipping method
            ->waitUntilIdle()
            ->click('@continue') // go to payment step
            ->waitUntilIdle()
            ->click('@method-0') // select payment method
            ->waitUntilIdle()
            ->click('@continue') // place order
            ->waitUntilIdle()
            ->assertPresent('@checkout-success');

        return $browser;
    }
}
