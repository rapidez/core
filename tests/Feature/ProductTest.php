<?php

namespace Rapidez\Core\Tests\Feature;

use Rapidez\Core\Models\Product;
use Rapidez\Core\Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * @test
     */
    public function supported_product_types_can_be_received()
    {
        foreach (['simple', 'configurable', 'downloadable', 'grouped'] as $supportedType) {
            $this->assertInstanceOf(
                Product::class,
                Product::selectAttributes(['sku'])->where((new Product())->getTable().'.type_id', $supportedType)->first()
            );
        }
    }

    /**
     * @test
     */
    public function unsupported_product_types_can_not_be_received()
    {
        foreach (['bundle'] as $unsupportedType) {
            $this->assertNull(Product::selectAttributes(['sku'])->where((new Product())->getTable().'.type_id', $unsupportedType)->first());
        }
    }
}
