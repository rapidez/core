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
            $browser->visit('/')->waitUntilVueLoaded()->assertSee(__('All rights reserved.'));
        });
    }
}
