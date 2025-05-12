<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    // Magento_Catalog
    Route::get('catalog/category/view/id/{categoryId}', [config('rapidez.routing.controllers.category'), 'show'])->whereNumber('categoryId');
    Route::get('catalog/product/view/id/{productId}', [config('rapidez.routing.controllers.product'), 'show'])->whereNumber('productId');
    Route::get('catalog/product/view/id/{productId}/{any?}', function ($productId) {
        return redirect('catalog/product/view/id/' . $productId, 301);
    })->where('any', '.*');

    // Magento_CatalogSearch
    Route::get('catalogsearch/result', function () {
        return redirect('/search?q=' . request()->get('q'), 301);
    });

    // Magento_Checkout
    Route::permanentRedirect('checkout/cart', route('cart'));
});
