<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;

class OnestepCheckoutTest extends CheckoutTest
{
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = file_get_contents(__DIR__ . '/../../config/rapidez/frontend.php');
        $config = config('rapidez.frontend');
        $config['checkout_steps']['default'] = ['onestep'];
        file_put_contents(__DIR__ . '/../../config/rapidez/frontend.php', '<?php return ' . var_export($config, true) . ';');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        file_put_contents(__DIR__ . '/../../config/rapidez/frontend.php', $this->config);
    }

    public function doCheckout(Browser $browser, $email = false, $password = false, $register = false)
    {
        $this->doCheckoutLogin($browser, $email, $password, $register);
        $this->doCheckoutShippingAddress($browser);
        $this->doCheckoutShippingMethod($browser);
        $this->doCheckoutPaymentMethod($browser);

        $browser
            ->waitUntilIdle()
            ->assertFormValid()
            ->click('@continue') // place order
            ->waitUntilIdle()
            ->waitFor('@checkout-success', 15)
            ->assertPresent('@checkout-success');

        return $browser;
    }
}
