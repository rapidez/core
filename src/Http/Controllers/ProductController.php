<?php

namespace Rapidez\Core\Http\Controllers;

class ProductController
{
    public function show(int $productId)
    {
        $productModel = config('rapidez.models.product');
        $product = $productModel::selectForProductPage()->findOrFail($productId);
        $attributes = ['id', 'name', 'sku', 'super_attributes', 'children', 'price', 'images'];

        foreach ($product->super_attributes ?: [] as $superAttribute) {
            $attributes[] = $superAttribute->code;
        }

        config(['frontend.product' => $product->only($attributes)]);

        return view('rapidez::product.overview', compact('product'));
    }
}
