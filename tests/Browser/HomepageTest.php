<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class HomepageTest extends DuskTestCase
{
    /**
     * @test
     */
    public function homepage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')->screenshot('Homepage')->assertSee('All rights reserved.');
        });
    }
}
