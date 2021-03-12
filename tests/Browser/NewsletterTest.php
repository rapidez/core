<?php

namespace Rapidez\Core\Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class NewsletterTest extends DuskTestCase
{
    use WithFaker;

    public function test()
    {
        $this->browse(function (Browser $browser) {
            $email = $this->faker->email;
            $browser->visit('/')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->type('@newsletter-email', $email)
                    ->click('@newsletter-submit')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->assertSee('Thank you for subscribing!')

                    ->waitForText('We care about the protection of your data.')
                    ->type('@newsletter-email', $email)
                    ->click('@newsletter-submit')
                    ->waitUntilAllAjaxCallsAreFinished()
                    ->assertSee('This email address is already subscribed.');
        });
    }
}
