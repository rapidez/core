<?php

namespace Rapidez\Core\Tests\Browser;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class NewsletterTest extends DuskTestCase
{
    use WithFaker;

    /**
     * @test
     */
    public function test()
    {
        $this->browse(function (Browser $browser) {
            $email = $this->faker->email;
            $browser->visit('/');
            $browser->script("localStorage['cookie-notice'] = true;");
            $browser->scrollIntoView('@newsletter')
                    ->waitUntilIdle()
                    ->type('@newsletter-email', $email)
                    ->click('@newsletter-submit')
                    ->waitUntilIdle()
                    ->assertSee('Thank you for subscribing!')

                    ->waitForText('We care about the protection of your data.')
                    ->type('@newsletter-email', $email)
                    ->click('@newsletter-submit')
                    ->waitUntilIdle()
                    ->assertSee('This email address is already subscribed.');
        });
    }
}
