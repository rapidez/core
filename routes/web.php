<?php

use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Http\Middleware\AuthenticateHealthCheck;

Route::get('healthcheck', config('rapidez.routing.controllers.healthcheck'))->middleware(AuthenticateHealthCheck::class);
Route::get('robots.txt', fn () => response(Rapidez::config('design/search_engine_robots/custom_instructions'))
    ->header('Content-Type', 'text/plain; charset=UTF-8'));

Route::middleware('web')->group(function () {
    Route::get('catalog/product/view/id/{productId}', [config('rapidez.routing.controllers.product'), 'show']);
    Route::get('catalog/category/view/id/{categoryId}', [config('rapidez.routing.controllers.category'), 'show']);

    Route::view('cart', 'rapidez::cart.overview')->name('cart');
    Route::get('checkout/{step?}', config('rapidez.routing.controllers.checkout'))->middleware('auth:magento-cart')->name('checkout');
    Route::get('search', config('rapidez.routing.controllers.search'))->name('search');
    Route::fallback(config('rapidez.routing.controllers.fallback'));
});
