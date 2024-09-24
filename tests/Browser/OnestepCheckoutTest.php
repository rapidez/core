<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class CheckoutTest extends DuskTestCase
{
    protected $config;
    protected function setUp(): void
    {
        $this->config = $config = config('rapidez.frontend');
        $config['checkout_steps']['default'] = ['onestep'];
        file_put_contents(__DIR__.'../..//config/rapidez/frontend.php', print_r($config, true));
        parent::setUp();
    }

    protected function tearDown(): void
    {
        file_put_contents(__DIR__.'../..//config/rapidez/frontend.php', print_r($this->config, true));
        parent::tearDown();
    }
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
        $browser
            ->visit('/checkout')
            ->waitUntilIdle()
            ->type('@email', $email ?: 'wayne@enterprises.com')
            ->waitUntilIdle();

        if ($password && ! $register) {
            $browser
                ->type('@password', $password)
                ->waitUntilIdle();
        } else if ($password && $register) {
            $browser->click('@create_account')
                ->waitUntilIdle()
                ->typeSlowly('@password', $password)
                ->typeSlowly('@password_repeat', $password)
                ->typeSlowly('@firstname', 'Bruce')
                ->typeSlowly('@lastname', 'Wayne')
                ->waitUntilIdle();
        }

        $browser
            ->type('@shipping_firstname', 'Bruce')
            ->type('@shipping_lastname', 'Wayne')
            ->type('@shipping_postcode', '72000')
            ->type('@shipping_housenumber', '1007')
            ->type('@shipping_street', 'Mountain Drive')
            ->type('@shipping_city', 'Gotham')
            ->select('@shipping_country', 'NL')
            ->type('@shipping_telephone', '530-7972')
            ->keys('@shipping_telephone', '{tab}')
            ->waitUntilIdle();

        $browser
            ->scrollIntoView('@shipping-method-0')
            ->click('@shipping-method-0') // select shipping method
            ->waitUntilIdle()
            ->click('@payment-method-0') // select payment method
            ->waitUntilIdle()
            ->click('@continue') // place order
            ->waitUntilIdle()
            ->waitFor('@checkout-success', 15)
            ->assertPresent('@checkout-success');

        return $browser;
    }
}
