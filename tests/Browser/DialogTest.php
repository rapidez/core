<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class DialogTest extends DuskTestCase
{
    /**
     * @test
     */
    public function test()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/?show-cookie-notice')
                    ->assertSee('Accept cookies')
                    ->waitForReload(function (Browser $browser) {
                        $browser->press('Accept cookies');
                    })
                    ->assertScript("window.localStorage['cookie-notice']");
        });
    }
}
