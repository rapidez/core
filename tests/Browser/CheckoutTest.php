<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class CheckoutTest extends DuskTestCase
{
    /**
     * @test
     */
    public function checkoutAsGuest()
    {
        $this->browse(function (Browser $browser) {
            $this->addProductToCart($browser);
            $this->doCheckout($browser, 'wayne+' . mt_rand() . '@enterprises.com');
        });
    }

    /**
     * @test
     */
    public function checkoutAsUser()
    {
        $email = 'wayne+' . mt_rand() . '@enterprises.com';

        // Go through checkout as guest and register.
        $this->browse(function (Browser $browser) use ($email) {
            $this->addProductToCart($browser);
            $this->doCheckout($browser, $email, 'IronManSucks.91939', true);
        });

        // Go through checkout as guest and log in.
        $this->browse(function (Browser $browser) use ($email) {
            $browser->waitForReload(fn ($browser) => $browser->visit('/'), 4)
                ->waitUntilIdle()
                ->waitFor('@account_menu')
                ->click('@account_menu')
                ->click('@logout')
                ->waitUntilIdle();
            $this->addProductToCart($browser);
            $this->doCheckout($browser, $email, 'IronManSucks.91939', false);
        });
    }

    public function addProductToCart($browser)
    {
        $browser
            ->visit($this->testProduct->url)
            ->waitUntilIdle()
            ->click('@add-to-cart')
            ->waitUntilIdle();

        return $browser;
    }

    public function doCheckout(Browser $browser, $email = false, $password = false, $register = false)
    {
        $browser
            ->visit('/checkout')
            ->pause(5000)
            ->waitUntilIdle()
            ->type('@email', $email ?: 'wayne@enterprises.com')
            ->click('@continue')
            ->waitUntilIdle();

        if ($password && ! $register) {
            $browser
                ->type('@password', $password)
                ->waitUntilIdle()
                ->click('@continue') // login
                ->waitUntilIdle();
        }

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
            ->waitUntilIdle()
            ->assertFormValid('form')
            ->waitUntilIdle();

        if ($password && $register) {
            $browser->click('@create_account')
                ->waitUntilIdle()
                ->typeSlowly('@password', $password)
                ->typeSlowly('@password_repeat', $password)
                ->assertFormValid('form')
                ->waitUntilIdle()
                ->pause(3000);
        }

        $browser
            ->waitForText(__('Shipping method'))
            ->scrollIntoView('@method-0')
            ->click('@method-0') // select shipping method
            ->waitUntilIdle()
            ->assertFormValid('form')
            ->scrollIntoView('@continue')
            ->click('@continue') // go to payment step
            ->waitUntilIdle()
            ->waitForText(__('Payment method'))
            ->click('@method-0') // select payment method
            ->waitUntilIdle()
            ->click('@continue') // place order
            ->waitUntilIdle()
            ->waitFor('@checkout-success', 15)
            ->assertPresent('@checkout-success');

        return $browser;
    }
}
