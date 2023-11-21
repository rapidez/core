<?php

namespace Rapidez\Core\Tests\Browser;

use Laravel\Dusk\Browser;
use Rapidez\Core\Tests\DuskTestCase;

class PriceTest extends DuskTestCase
{
    /**
     * @test
     */
    public function priceBaseWithoutTaxShowWithoutTax()
    {
        $this->browseWithConfigs([
            'tax/calculation/price_includes_tax' => 0,
            'tax/display/type'                   => 1,
        ], function (Browser $browser) {
            $browser
                ->visit($this->testProduct->url)
                ->assertSeePrice($this->testProduct->price_without_tax);
        });
    }

    /**
     * @test
     */
    public function priceBaseWithoutTaxShowWithTax()
    {
        $this->browseWithConfigs([
            'tax/calculation/price_includes_tax' => 0,
            'tax/display/type'                   => 2,
        ], function (Browser $browser) {
            $browser
                ->visit($this->testProduct->url)
                ->assertSeePrice($this->testProduct->price);
        });
    }

    /**
     * @test
     */
    public function priceBaseWithTaxShowWithoutTax()
    {
        dump($this->testProduct->price_without_tax);
        $this->browseWithConfigs([
            'tax/calculation/price_includes_tax' => 1,
            'tax/display/type'                   => 1,
        ], function (Browser $browser) {
            $browser
                ->visit($this->testProduct->url)
                ->assertSeePrice($this->testProduct->price_without_tax);
        });
    }

    /**
     * @test
     */
    public function priceBaseWithTaxShowWithTax()
    {
        $this->browseWithConfigs([
            'tax/calculation/price_includes_tax' => 1,
            'tax/display/type'                   => 2,
        ], function (Browser $browser) {
            $browser
                ->visit($this->testProduct->url)
                ->assertSeePrice($this->testProduct->price);
        });
    }

    /**
     * @test
     */
    public function specialPrice()
    {
        $previousSpecialPrice = $this->testProduct->special_price;
        $this->testProduct->newQueryWithoutScopes()->update(['special_price' => 20]);

        try {
            $this->browse(function (Browser $browser) {
                $browser
                    ->visit($this->testProduct->url)
                    ->assertSeePrice($this->testProduct->refresh()->special_price);
            });
        } finally {
            $this->testProduct->newQueryWithoutScopes()->update(['special_price' => $previousSpecialPrice]);
        }
    }

    // TODO: Test special price dates.
}
