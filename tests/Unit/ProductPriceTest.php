<?php

namespace Rapidez\Core\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
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
        $product = Product::find(4);

        $product->tierPrices()->create(['qty' => 5, 'value' => 40, 'website_id' => 1, 'all_groups' => 1, 'customer_group_id' => 0]);
        $product->tierPrices()->create(['qty' => 10, 'value' => 35, 'website_id' => 1, 'all_groups' => 1, 'customer_group_id' => 0]);
        $product->tierPrices()->create(['qty' => 15, 'value' => 40, 'website_id' => 1, 'all_groups' => 1, 'customer_group_id' => 0]);

        $product->tierPrices()->create(['qty' => 1, 'value' => 43, 'website_id' => 1, 'all_groups' => 0, 'customer_group_id' => 2]);
        $product->tierPrices()->create(['qty' => 5, 'value' => 39, 'website_id' => 1, 'all_groups' => 0, 'customer_group_id' => 2]);
        $product->tierPrices()->create(['qty' => 15, 'value' => 34, 'website_id' => 1, 'all_groups' => 0, 'customer_group_id' => 2]);

        $product->tierPrices()->create(['qty' => 20, 'value' => 30, 'website_id' => 0, 'all_groups' => 1, 'customer_group_id' => 0]);

        // All groups
        $this->assertEquals(45 * 4, $product->getPrice(4), 'Tier price for all groups, qty 4');
        $this->assertEquals(40 * 5, $product->getPrice(5), 'Tier price for all groups, qty 5');
        $this->assertEquals(35 * 12, $product->getPrice(12), 'Tier price for all groups, qty 12');
        $this->assertEquals(35 * 15, $product->getPrice(15), 'Tier price for all groups, qty 15');
        $this->assertEquals(30 * 23, $product->getPrice(23), 'Tier price for all groups, qty 23');

        // Specifically customer group 2
        $this->assertEquals(43 * 4, $product->getPrice(4, 2), 'Tier price for group 2, qty 4');
        $this->assertEquals(39 * 5, $product->getPrice(5, 2), 'Tier price for group 2, qty 5');
        $this->assertEquals(35 * 12, $product->getPrice(12, 2), 'Tier price for group 2, qty 12');
        $this->assertEquals(34 * 15, $product->getPrice(15, 2), 'Tier price for group 2, qty 15');
        $this->assertEquals(30 * 22, $product->getPrice(22, 2), 'Tier price for group 2, qty 22');

        // This is technically not valid, but without creating a whole new website this is the best we can do in a unit test.
        config()->set('rapidez.website', 0);
        $this->assertEquals(45 * 15, $product->getPrice(15, 2), 'Tier price on website 0 for group 2, qty 15');
        $this->assertEquals(45 * 15, $product->getPrice(15), 'Tier price on website 0 for all groups, qty 15');
        $this->assertEquals(30 * 21, $product->getPrice(21), 'Tier price on website 0 for all groups, qty 21');
    }

    #[Test]
    public function product_can_combine_tier_prices_and_special_price()
    {
        $product = Product::find(10);

        $product->tierPrices()->create(['qty' => 5, 'value' => 30, 'website_id' => 1, 'all_groups' => 1, 'customer_group_id' => 0]);
        $product->tierPrices()->create(['qty' => 10, 'value' => 20, 'website_id' => 1, 'all_groups' => 1, 'customer_group_id' => 0]);

        // All groups
        $this->assertEquals(24 * 4, $product->getPrice(4), 'Tier price (should be special price) for all groups, qty 4');
        $this->assertEquals(24 * 5, $product->getPrice(5), 'Tier price (should be special price) for all groups, qty 5');
        $this->assertEquals(20 * 12, $product->getPrice(12), 'Tier price for all groups, qty 12');
    }
}
