<?php

namespace Rapidez\Core\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Rapidez\Core\Events\ProductViewEvent;
use Rapidez\Core\Models\Config;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Tests\TestCase;

class ProductTest extends TestCase
{
    #[Test]
    public function products_can_be_retrieved()
    {
        foreach (['simple', 'configurable', 'downloadable', 'grouped'] as $supportedType) {
            $this->assertInstanceOf(
                Product::class,
                Product::firstWhere('type_id', $supportedType),
                'Products of type ' . $supportedType . ' are not being retrieved.',
            );
        }
    }

    #[Test]
    public function disabled_products_cannot_be_retrieved()
    {
        $this->assertEquals(0, Product::whereAttribute('status', 0)->count(), 'Disabled products are being retrieved by default.');
    }

    #[Test]
    public function product_has_gallery()
    {
        $product = Product::find(100);

        $this->assertNotNull($product->gallery, 'Product 100 does not have a gallery.');
        $this->assertEquals(3, count($product->gallery), 'Product 100 does not have 3 images in the gallery.');
        $this->assertEquals(3, count($product->images), 'Product 100 does not have 3 images in the images collection.');
    }

    #[Test]
    public function product_can_have_children_and_multiple_parents()
    {
        $product = Product::find(45);

        $this->assertNotNull($product->children, 'Product 45 does not have children.');
        $this->assertEquals(3, count($product->children), 'Product 45 does not have 3 children.');
        $this->assertEquals(2, count($product->children->first()->parents), 'Product 45\'s first child does not have 2 parents.');
    }

    #[Test]
    public function product_can_have_upsells_and_crosssells()
    {
        $product = Product::find(1578);

        $this->assertEquals(8, $product->getLinkedProducts('up_sell')->count(), 'Product 1578 does not have 8 upsells.');
        $this->assertEquals(4, $product->getLinkedProducts('cross_sell')->count(), 'Product 1578 does not have 4 crosssells.');
    }

    #[Test]
    public function product_can_have_categories()
    {
        $product = Product::find(847);

        $this->assertEquals(6, $product->categoryProducts->count(), 'Product 847 does not have 6 categories attached.');
    }

    #[Test]
    public function product_has_stock_data()
    {
        $product = Product::find(99);

        $this->assertNotNull($product->stock, 'Product 99 does not have stock data.');
        $this->assertEquals(100, $product->stock->qty, 'Product 99 does not have 100 items in stock.');
        $this->assertTrue($product->stock->is_in_stock, 'Product 99 is not in stock.');
        $this->assertEquals(1, $product->stock->min_sale_qty, 'Product 99 does not have a min sale qty of 1.');
    }

    #[Test]
    public function product_can_have_options()
    {
        // TODO: We should also visually test this!
        // We could create feature tests for that
        // but Playwright is better: RAP-1541
        $product = Product::find(3);

        $name = $product->options()->create(['type' => 'field']);
        $name->titles()->create(['title' => 'Name', 'store_id' => 0]);
        $name->titles()->create(['title' => 'Naam', 'store_id' => 1]);
        $name->prices()->create(['price' => 5, 'store_id' => 0]);
        $name->prices()->create(['price' => 10, 'store_id' => 1]);

        $description = $product->options()->create(['type' => 'area']);
        $description->titles()->create(['title' => 'Description']);
        $description->prices()->create(['price' => 20, 'price_type' => 'percent']);

        $color = $product->options()->create(['type' => 'drop_down']);
        $color->titles()->create(['title' => 'Color']);

        $red = $color->values()->create();
        $red->titles()->create(['title' => 'Red', 'store_id' => 0]);
        $red->titles()->create(['title' => 'Rood', 'store_id' => 1]);
        $red->prices()->create(['price' => 1]);

        $blue = $color->values()->create();
        $blue->titles()->create(['title' => 'Blue']);
        $blue->prices()->create(['price' => 5, 'store_id' => 0]);
        $blue->prices()->create(['price' => 2.5, 'store_id' => 1]);

        $options = $product->options()->get();

        $this->assertEquals(3, $options->count());

        $this->assertEquals('Naam', $options->get(0)->title);
        $this->assertEquals('+ €10.00', $options->get(0)->price_label);

        $this->assertEquals('Description', $options->get(1)->title);
        $this->assertEquals('+ 20%', $options->get(1)->price_label);

        $dropdown = $options->get(2)->values;

        $this->assertEquals('Rood', $dropdown->get(0)->title);
        $this->assertEquals('+ €1.00', $dropdown->get(0)->price_label);

        $this->assertEquals('Blue', $dropdown->get(1)->title);
        $this->assertEquals('+ €2.50', $dropdown->get(1)->price_label);
    }

    #[Test]
    public function product_can_have_tier_prices()
    {
        $this->assertTrue(true);
        // TODO: Base DB doesn't have tier prices. Test this some other way.
    }

    #[Test]
    public function product_can_have_review_data()
    {
        $product = Product::find(37);

        $this->assertNotNull($product->reviewSummary, 'Product 37 does not have review data.');
        $this->assertEquals(3, $product->reviewSummary->reviews_count, 'Product 37 does not have 3 reviews in the summary.');
        $this->assertEquals(87, $product->reviewSummary->rating_summary, 'Product 37 does not have an average review score of 87 in the summary.');
    }

    #[Test]
    public function product_can_have_views()
    {
        // Enable the reports
        Config::create(['path' => 'reports/options/enabled', 'value' => 1]);
        Config::create(['path' => 'reports/options/product_view_enabled', 'value' => 1]);

        $product = Product::find(1);

        $this->assertEquals(0, $product->views()->count());

        ProductViewEvent::dispatch($product);
        ProductViewEvent::dispatch($product);

        $this->assertEquals(2, $product->views()->count());
    }

    #[Test]
    public function product_has_prices()
    {
        $product = Product::find(10);

        $this->assertEquals(32, $product->price, 'Product price did not return the correct value.');
        $this->assertEquals(24, $product->special_price, 'Product special price did not return the correct value.');
    }

    #[Test]
    public function product_catalog_price_rules_apply()
    {
        // There is a rule: "20% off all Women’s and Men’s Pants"
        $product = Product::find(737);

        $this->assertEquals(35.0, (float)$product->customAttributes['price']->value, 'Product original price did not return the correct value.');
        $this->assertEquals(28.0, (float)$product->price, 'Product discounted price did not return the correct value.');
        $this->assertEquals(28.0, (float)$product->toArray()['price'], 'Product discounted price did not return the correct value.');
    }

    #[Test]
    public function product_has_custom_attributes()
    {
        $product = Product::find(10);

        $this->assertEqualsCanonicalizing([8, 11], iterator_to_array($product->activity->value), 'Activity attribute on product 10 did not return the right values.');
        $this->assertEquals('Gym, Yoga', $product->activity->label, 'Activity attribute on product 10 did not yield the right text output.');

        $this->assertEquals('Savvy Shoulder Tote', $product->name->label, 'Name attribute on product 10 did not return the right value.');
        $this->assertEquals($product->name->label, $product->name, 'Product name did not return the right value or did not cast its value correctly.');
    }

    #[Test]
    public function product_indexes_custom_attributes()
    {
        $product = Product::find(68);

        $data = $product->toSearchableArray();

        $this->assertEquals('All-Weather, Cool, Indoor, Spring, Windy', $data['climate']['label'], 'Product 68 did not get indexed with the right climate label.');
        $this->assertEquals('Chaz Kangeroo Hoodie', $data['name'], 'Product 68 did not get indexed with the right name.');
    }

    #[Test]
    public function product_indexes_super_attributes()
    {
        $product = Product::find(68);

        $data = $product->toSearchableArray();
        $firstChild = $data['children'][53];

        $this->assertEquals(['value' => 166, 'label' => 'XS'], $firstChild['size'], 'Child product under product 68 did not get indexed with the right size data.');

    }
}
