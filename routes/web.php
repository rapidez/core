<?php

use Rapidez\Core\Models\Category;
use Rapidez\Core\Models\Page;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\Rewrite;

Route::middleware('web')->group(function () {
    Route::view('cart', 'cart.overview');
    Route::view('checkout', 'checkout.overview');

    Route::fallback(function ($url = null) {
        if ($rewrite = Rewrite::firstWhere('request_path', $url)) {
            if ($rewrite->entity_type == 'category') {
                if ($category = Category::find($rewrite->entity_id)) {
                    config(['frontend.category' => $category->only('entity_id')]);
                    return view('category.overview', compact('category'));
                }
            }

            if ($rewrite->entity_type == 'product') {
                if ($product = Product::selectForProductPage()->find($rewrite->entity_id)) {
                    $attributes = ['sku', 'super_attributes'];
                    foreach ($product->super_attributes ?: [] as $superAttribute) {
                        $attributes[] = $superAttribute->code;
                    }
                    config(['frontend.product' => $product->only($attributes)]);
                    return view('product.overview', compact('product'));
                }
            }
        }

        if ($page = Page::firstWhere('identifier', $url ?: 'home')) {
            return view('page.overview', compact('page'));
        }

        abort(404);
    });
});
