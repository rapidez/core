<?php

namespace Rapidez\Core\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Rapidez\Core\Events\ProductViewEvent;
use Rapidez\Core\Models\Config;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Tests\TestCase;

class ProductPriceTest extends TestCase
{
    #[Test]
    public function product_has_prices()
    {
        $product = Product::find(10);

        $this->assertEquals(32.0, (float) $product->customAttributes['price']->value);
        $this->assertEquals(24.0, (float) $product->customAttributes['special_price']->value);

        $this->assertEquals(32.0, (float) $product->price);
        $this->assertEquals(32.0, (float) $product->toArray()['price']);

        $this->assertEquals(24.0, (float) $product->special_price);
        $this->assertEquals(24.0, (float) $product->toArray()['special_price']);
    }

    #[Test]
    public function product_equal_special_price_is_null()
    {
        $product = Product::find(2);

        $this->assertEquals(32.0, (float) $product->customAttributes['price']->value);
        $this->assertEquals(32.0, (float) $product->customAttributes['special_price']->value);

        $this->assertEquals(32.0, (float) $product->price);
        $this->assertEquals(32.0, (float) $product->toArray()['price']);

        $this->assertNull($product->special_price);
        $this->assertNull($product->toArray()['special_price']);
    }

    #[Test]
    public function product_catalog_price_rules_apply()
    {
        // There is a rule: "20% off all Women’s and Men’s Pants"
        $product = Product::find(737);

        $this->assertEquals(35.0, (float) $product->customAttributes['price']->value);
        $this->assertEquals(35.0, (float) $product->price);
        $this->assertEquals(35.0, (float) $product->toArray()['price']);

        $this->assertEquals(28.0, (float) $product->special_price);
        $this->assertEquals(28.0, (float) $product->toArray()['special_price']);
    }

    #[Test]
    public function product_can_have_tier_prices()
    {
        $this->assertTrue(true);
        // TODO: Base DB doesn't have tier prices. Test this some other way.
    }
}
