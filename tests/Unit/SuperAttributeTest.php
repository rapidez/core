<?php

namespace Rapidez\Core\Tests\Feature;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\SuperAttribute;
use Rapidez\Core\Tests\TestCase;

class SuperAttributeTest extends TestCase
{
    #[Test]
    public function super_attribute_uses_the_store_label()
    {
        DB::table('eav_attribute_label')->insert([
            ['attribute_id' => 93, 'store_id' => 1, 'value' => 'Kleur'],
            ['attribute_id' => 93, 'store_id' => 0, 'value' => 'Colour'],
        ]);

        $this->assertEquals('Kleur', SuperAttribute::find(3)->frontend_label, 'Super attribute 3 did not use the store label.');
        $this->assertEquals('Size', SuperAttribute::find(2)->frontend_label, 'Super attribute 2 did not use the store label.');
        $this->assertEquals(1, SuperAttribute::where('product_super_attribute_id', 3)->count(), 'Super attribute 3 got duplicated by labels from other stores.');
    }

    #[Test]
    public function super_attribute_label_falls_back_to_frontend_label_and_attribute_code()
    {
        $this->assertEquals('Color', SuperAttribute::find(3)->frontend_label, 'Super attribute 3 did not fall back to the frontend label.');

        DB::table('eav_attribute')->where('attribute_id', 93)->update(['frontend_label' => null]);

        $this->assertEquals('color', SuperAttribute::find(3)->frontend_label, 'Super attribute 3 did not fall back to the attribute code.');
    }

    #[Test]
    public function product_super_attributes_are_ordered_by_position()
    {
        $this->assertEquals(['size', 'color'], Product::find(68)->superAttributes->pluck('attribute_code')->all(), 'Super attributes on product 68 are not ordered by position.');

        DB::table('catalog_product_super_attribute')->where('product_id', 68)->update(['position' => 0]);

        $this->assertEquals(['color', 'size'], Product::find(68)->superAttributes->pluck('attribute_code')->all(), 'Super attributes on product 68 are not ordered by attribute code when positions are equal.');
    }
}
