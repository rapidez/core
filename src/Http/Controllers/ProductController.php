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
            'max_sale_qty',
            'qty_increments',
        ];

        $attributes = Eventy::filter('productpage.frontend.attributes', $attributes);

        foreach ($product->super_attributes ?: [] as $superAttribute) {
            $attributes[] = 'super_' . $superAttribute->code;
        }

        ProductViewEvent::dispatch($product);

        config(['frontend.product' => $product->only($attributes)]);

        $response = response()->view('rapidez::product.overview', compact('product'));
        $response->setCache(['etag' => md5($response->getContent() ?? ''), 'last_modified' => $product->updated_at]);
        $response->isNotModified(request());
        return $response;
    }
}
