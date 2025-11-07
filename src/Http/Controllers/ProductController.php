<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Events\ProductViewEvent;
use Rapidez\Core\Models\Product;
use TorMorten\Eventy\Facades\Eventy;

class ProductController
{
    public function show(int $productId)
    {
        $productModel = config('rapidez.models.product');

        /** @var \Rapidez\Core\Models\Product $product */
        $product = $productModel::query()
            ->whereInAttribute('visibility', [
                Product::VISIBILITY_IN_CATALOG,
                Product::VISIBILITY_BOTH,
            ])
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
            'in_stock',
            'min_sale_qty',
            'max_sale_qty',
            'qty_increments',
            ...$product->superAttributes->pluck('attribute_code'),
        ];

        $attributes = Eventy::filter('productpage.frontend.attributes', $attributes);

        // TODO: Make this neater so that we can maybe get this data from the product directly?
        // Alternatively we can refactor the frontend to not need so much customized data
        $data = $product->only($attributes);
        foreach ($product->superAttributeValues as $attribute => $values) {
            $data['super_' . $attribute] = $values->pluck('value');
        }

        $queryOptions = request()->query;
        $selectedOptions = [];

        foreach ($product->superAttributes ?: [] as $superAttributeId => $superAttribute) {
            // Make sure we only check for query options that exist
            if ($queryOptions->has($superAttribute->attribute_code)) {
                $selectedOptions[$superAttribute->attribute_code] = $queryOptions->get($superAttribute->attribute_code);
            }
        }

        // Find the first child that matches the given product options
        $selectedChild = $product->children->firstWhere(function ($child) use ($selectedOptions) {
            return count($selectedOptions) && collect($selectedOptions)->every(fn ($value, $code) => $child->{$code} == $value);
        }) ?? $product;

        ProductViewEvent::dispatch($product);

        config(['frontend.product' => $data]);

        $response = response()->view('rapidez::product.overview', compact('product', 'selectedChild'));

        return $response
            ->setEtag(md5($response->getContent() ?? ''))
            ->setLastModified($product->updated_at);
    }
}
