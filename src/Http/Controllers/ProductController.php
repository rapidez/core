<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Models\Product;

class ProductController
{
    public function show(int $productId)
    {
        $product = Product::selectForProductPage()->findOrFail($productId);

        $attributes = ['name', 'sku', 'super_attributes', 'children', 'price', 'images'];
        foreach ($product->super_attributes ?: [] as $superAttribute) {
            $attributes[] = $superAttribute->code;
        }

        config(['frontend.product' => $product->only($attributes)]);

        return view('rapidez::product.overview', compact('product'));
    }
}
