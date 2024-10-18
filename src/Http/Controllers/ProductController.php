<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Contracts\View\View;
use Rapidez\Core\Events\ProductViewEvent;
use TorMorten\Eventy\Facades\Eventy;

class ProductController
{
    public function show(int $productId): View
    {
        $productModel = config('rapidez.models.product');
        $product = $productModel::selectForProductPage()
            ->withEventyGlobalScopes('productpage.scopes')
            ->with('options')
            ->findOrFail($productId);

        $attributes = [
            'entity_id',
            'name',
            'sku',
            'super_attributes',
            'children',
            'grouped',
            'options',
            'price',
            'special_price',
            'images',
            'url',
            'min_sale_qty',
            'qty_increments',
        ];

        $attributes = Eventy::filter('productpage.frontend.attributes', $attributes);

        foreach ($product->super_attributes ?: [] as $superAttribute) {
            $attributes[] = 'super_' . $superAttribute->code;
        }

        ProductViewEvent::dispatch($product);

        config(['frontend.product' => $product->only($attributes)]);

        return view('rapidez::product.overview', compact('product'));
    }
}
