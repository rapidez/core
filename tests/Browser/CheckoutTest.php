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
                ->click('@logout');
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
        } else {
            $browser
                ->waitFor('@shipping_country', 15)
                ->typeSlowly('@shipping_firstname', 'Bruce')
                ->waitUntilIdle()
                ->typeSlowly('@shipping_lastname', 'Wayne')
                ->waitUntilIdle()
                ->typeSlowly('@shipping_postcode', '72000')
                ->waitUntilIdle()
                ->typeSlowly('@shipping_housenumber', '1007')
                ->waitUntilIdle()
                ->typeSlowly('@shipping_street', 'Mountain Drive')
                ->waitUntilIdle()
                ->typeSlowly('@shipping_city', 'Gotham')
                ->waitUntilIdle()
                ->select('@shipping_country', 'NL')
                ->waitUntilIdle()
                ->typeSlowly('@shipping_telephone', '530-7972')
                ->waitUntilIdle()
                ->assertFormValid('form')
                ->waitUntilIdle();
        }

        if ($password && $register) {
            $browser->click('@create_account')
                ->waitUntilIdle()
                ->typeSlowly('@password', $password)
                ->typeSlowly('@password_repeat', $password)
                ->assertFormValid('form')
                ->waitUntilIdle();
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
            ->assertPresent('@checkout-success');

        return $browser;
    }
}
