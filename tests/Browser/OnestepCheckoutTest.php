<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;

class OnestepCheckoutTest extends CheckoutTest
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

    public function doCheckout(Browser $browser, $email = false, $password = false, $register = false)
    {
        $this->doCheckoutLogin($browser, $email, $password, $register);
        $this->doCheckoutShippingAddress($browser);
        $this->doCheckoutShippingMethod($browser);
        $this->doCheckoutPaymentMethod($browser);

        $browser
            ->click('@continue') // place order
            ->waitUntilIdle()
            ->waitFor('@checkout-success', 15)
            ->assertPresent('@checkout-success');

        return $browser;
    }
}
