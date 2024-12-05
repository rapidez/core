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
                ->waitUntilVueLoaded()
                ->waitUntilIdle()
                ->assertSee(__('Accept cookies'))
                ->waitForReload(function (Browser $browser) {
                    $browser->press(__('Accept cookies'));
                })
                ->assertScript("window.localStorage['cookie-notice']");
        });
    }
}
