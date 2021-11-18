<?php

namespace Rapidez\Core\Http\Controllers;

use TorMorten\Eventy\Facades\Eventy;

class ProductController
{
    public function show(int $productId)
    {
        $productModel = config('rapidez.models.product');
        $product = $productModel::selectForProductPage()->findOrFail($productId);
        $attributes = ['id', 'name', 'sku', 'super_attributes', 'children', 'price', 'special_price', 'images', 'url'];

        $attributes = Eventy::filter('frontend.product.attributes', $attributes);

        foreach ($product->super_attributes ?: [] as $superAttribute) {
            $attributes[] = $superAttribute->code;
        }

        config(['frontend.product' => $product->only($attributes)]);

        return view('rapidez::product.overview', compact('product'));
    }
}
