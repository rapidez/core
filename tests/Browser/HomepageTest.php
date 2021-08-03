<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class HomepageTest extends DuskTestCase
{
    public function testHomepage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')->assertSee('Hot Sellers');
        });
    }
}
