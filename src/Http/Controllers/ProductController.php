<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Events\ProductViewEvent;
use TorMorten\Eventy\Facades\Eventy;

class ProductController
{
    public function show(int $productId)
    {
        $productModel = config('rapidez.models.product');
        $product = $productModel::selectForProductPage()
            ->withEventyGlobalScopes('productpage.scopes')
            ->findOrFail($productId);

        $attributes = [
            'id',
            'name',
            'sku',
            'super_attributes',
            'children',
            'grouped',
            'price',
            'special_price',
            'images', 'url',
            'min_sale_qty',
            'tax'
        ];
        $attributes = Eventy::filter('productpage.frontend.attributes', $attributes);

        foreach ($product->super_attributes ?: [] as $superAttribute) {
            $attributes[] = 'super_'.$superAttribute->code;
        }

        ProductViewEvent::dispatch($product);

        config(['frontend.product' => $product->only($attributes)]);

        return view('rapidez::product.overview', compact('product'));
    }
}
