<?php

namespace Rapidez\Core\Tests\Browser;

use Exception;
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
            $browser->addProductToCart($this->testProduct->url);
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
            $browser->addProductToCart($this->testProduct->url);
            $this->doCheckout($browser, $email, 'IronManSucks.91939', true);
        });

        // Go through checkout as guest and log in.
        $this->browse(function (Browser $browser) use ($email) {
            $browser->waitForReload(fn ($browser) => $browser->visit('/'), 4)
                ->waitUntilVueLoaded()
                ->waitUntilIdle()
                ->waitFor('@account_menu')
                ->click('@account_menu')
                ->click('@logout')
                ->waitUntilIdle();
            $browser->addProductToCart($this->testProduct->url);
            $this->doCheckout($browser, $email, 'IronManSucks.91939', false);
        });
    }

    public function doCheckout(Browser $browser, $email = false, $password = false, $register = false)
    {
        $this->doCheckoutLogin($browser, $email, $password, $register)
            ->assertFormValid('form')
            ->click('@continue')
            ->waitUntilIdle();

        $this->doCheckoutShippingAddress($browser);

        $this->doCheckoutShippingMethod($browser)
            ->assertFormValid('form')
            ->scrollIntoView('@continue')
            ->click('@continue') // go to payment step
            ->waitUntilIdle();

        $this->doCheckoutPaymentMethod($browser)
            ->assertFormValid('form')
            ->click('@continue') // place order
            ->waitUntilIdle();

        $browser->waitFor('@checkout-success', 15)
            ->assertPresent('@checkout-success');

        return $browser;
    }

    public function doCheckoutLogin(Browser $browser, $email = false, $password = false, $register = false)
    {
        $browser
            ->visit('/checkout')
            ->waitUntilVueLoaded()
            ->waitUntilIdle()
            ->type('email', $email ?: 'wayne@enterprises.com')
            ->waitUntilIdle();

        if ($password && ! $register) {
            $browser
                ->type('password', $password)
                ->waitUntilIdle();
        } elseif ($password && $register) {
            $browser->click('@create_account')
                ->waitUntilIdle()
                ->typeSlowly('password', $password)
                ->typeSlowly('password_repeat', $password)
                ->typeSlowly('firstname', 'Bruce')
                ->typeSlowly('lastname', 'Wayne')
                ->waitUntilIdle();
        }

        return $browser;
    }

    public function doCheckoutShippingAddress(Browser $browser)
    {
        try {
            $browser->select('@shipping_address_select', '')
                ->waitUntilIdle();
        } catch (Exception $e) {
            // How Dusk internally handles nonexistent elements.
        }

        $browser
            ->type('shipping_firstname', 'Bruce' . mt_rand())
            ->type('shipping_lastname', 'Wayne')
            ->type('shipping_postcode', '72000')
            ->type('shipping_housenumber', '1007')
            ->type('shipping_street', 'Mountain Drive')
            ->type('shipping_city', 'Gotham')
            ->select('shipping_country', 'NL')
            ->type('shipping_telephone', '530-7972')
            ->keys('input[name=shipping_telephone]', '{tab}')
            ->waitUntilIdle();

        return $browser;
    }

    public function doCheckoutShippingMethod(Browser $browser)
    {
        $browser->scrollIntoView('@shipping-method-0')
            ->click('@shipping-method-0') // select shipping method
            ->waitUntilIdle();

        return $browser;
    }

    public function doCheckoutPaymentMethod(Browser $browser)
    {
        $browser->scrollIntoView('@payment-method-0')
            ->click('@payment-method-0') // select payment method
            ->waitUntilIdle();

        return $browser;
    }
}
