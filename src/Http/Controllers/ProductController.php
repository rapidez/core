<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Rapidez\Core\Events\ProductViewEvent;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Product;
use TorMorten\Eventy\Facades\Eventy;

class ProductController
{
    public function show(int $productId)
    {
        $productModel = config('rapidez.models.product');

        /** @var \Rapidez\Core\Models\Product $product */
        $product = $productModel::query()
            ->withEventyGlobalScopes('productpage.scopes')
            ->with(['tierPrices'])
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
            'options',
            'price',
            'special_price',
            'prices',
            'images',
            'media',
            'url',
            'stock',
            'review_summary',
            'tier_prices',

            ...$product->superAttributeCodes,
        ];

        $attributes = Eventy::filter('productpage.frontend.attributes', $attributes);
        $appends = array_filter($attributes, fn ($key) => $product->hasAnyGetMutator($key));
        $relations = array_filter(
            array_map(fn ($key) => Str::camel($key), $attributes),
            fn ($key) => $product->isRelation($key)
        );

        $data = Arr::only($product->append($appends)->loadMissing($relations)->toArray(), $attributes);

        $queryOptions = request()->query;
        $selectedOptions = [];

        foreach ($product->superAttributes ?: [] as $superAttributeId => $superAttribute) {
            // Make sure we only check for query options that exist
            if ($queryOptions->has($superAttribute->attribute_code)) {
                $selectedOptions[$superAttribute->attribute_code] = $queryOptions->get($superAttribute->attribute_code);
            }
        }

        // Find the first child that matches the given product options
        $selectedChild = $product->children->firstWhere(fn ($child) => count($selectedOptions) && collect($selectedOptions)->every(fn ($value, $code) => $child->getCustomAttribute($code)->value == $value)
        ) ?? $product;

        if (Rapidez::config('reports/options/enabled') && Rapidez::config('reports/options/product_view_enabled')) {
            View::composer('rapidez::layouts.app', fn ($view) => $view->getFactory()->startPush('foot', view('rapidez::product.partials.track')));
        }

        config(['frontend.product' => $data]);

        $response = response()->view('rapidez::product.overview', compact('product', 'selectedChild'));

        return $response
            ->setEtag(md5($response->getContent() ?? ''))
            ->setLastModified($product->updated_at);
    }

    public function track(int $productId)
    {
        $productModel = config('rapidez.models.product');

        /** @var \Rapidez\Core\Models\Product $product */
        $product = $productModel::findOrFail($productId);

        ProductViewEvent::dispatch($product);
    }
}
