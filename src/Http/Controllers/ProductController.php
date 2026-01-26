<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Rapidez\Core\Events\ProductViewEvent;
use Rapidez\Core\Models\EavAttribute;
use Rapidez\Core\Models\Product;
use TorMorten\Eventy\Facades\Eventy;

class ProductController
{
    public function show(int $productId)
    {
        $productModel = config('rapidez.models.product');
        $productModel::shouldBeStrict(! App::isProduction()); // Throw errors if relations are used but not eager loaded if not in production.

        $frontAttributes = EavAttribute::getCachedCatalog()->where('is_visible_on_front', 1)->pluck('attribute_id');
        /** @var \Rapidez\Core\Models\Product $product */
        $product = $productModel::query()
            ->whereInAttribute('visibility', [
                Product::VISIBILITY_IN_CATALOG,
                Product::VISIBILITY_BOTH,
            ])
            // It's important these are AFTER `whereInAttribute`
            // as it seems to reset the global scopes
            ->withoutGlobalscope('customAttributes')
            ->withGlobalScope('customAttributes', fn (Builder $builder) => $builder->withCustomAttributes(
                fn (Relation $q) => $q
                    ->whereIn($q->qualifyColumn('attribute_id'), $frontAttributes)
            )
            )
            ->withEventyGlobalScopes('productpage.scopes')
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
            'prices',
            'images',
            'media',
            'url',
            'in_stock',
            'min_sale_qty',
            'max_sale_qty',
            'qty_increments',

            ...$product->superAttributeCodes,
        ];

        $attributes = Eventy::filter('productpage.frontend.attributes', $attributes);

        $data = Arr::only($product->toArray(), $attributes);

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

        ProductViewEvent::dispatch($product);

        config(['frontend.product' => $data]);

        $response = response()->view('rapidez::product.overview', compact('product', 'selectedChild'));

        return $response
            ->setEtag(md5($response->getContent() ?? ''))
            ->setLastModified($product->updated_at);
    }
}
