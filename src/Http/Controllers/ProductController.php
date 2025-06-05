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

        $queryOptions = request()->query;
        $selectedOptions = [];

        $attributes = Eventy::filter('productpage.frontend.attributes', $attributes);

        foreach ($product->super_attributes ?: [] as $superAttributeId => $superAttribute) {
            $attributes[] = 'super_' . $superAttribute->code;

            // Make sure we only check for query options that exist
            if ($queryOptions->has($superAttribute->code)) {
                $selectedOptions[$superAttribute->code] = $queryOptions->get($superAttribute->code);
            }
        }

        // Find the first child that matches the given product options
        $selectedChild = collect($product->children)->firstWhere(function ($child) use ($selectedOptions) {
            return count($selectedOptions) && collect($selectedOptions)->every(fn ($value, $code) => $child->{$code} == $value);
        }) ?? $product;

        ProductViewEvent::dispatch($product);

        config(['frontend.product' => $product->only($attributes)]);

        $response = response()->view('rapidez::product.overview', compact('product', 'selectedChild'));

        return $response
            ->setEtag(md5($response->getContent() ?? ''))
            ->setLastModified($product->updated_at);
    }
}
