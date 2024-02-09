<?php

use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Http\Controllers\FallbackController;
use Rapidez\Core\Http\Controllers\HealthcheckController;
use Rapidez\Core\Http\Controllers\SearchController;
use Rapidez\Core\Http\Middleware\AuthenticateHealthCheck;

Route::get('healthcheck', HealthcheckController::class)->middleware(AuthenticateHealthCheck::class);
Route::get('robots.txt', fn () => response(Rapidez::config('design/search_engine_robots/custom_instructions'))
    ->header('Content-Type', 'text/plain; charset=UTF-8'));

Route::middleware('web')->group(function () {
    Route::get('catalog/product/view/id/{productId}', [config('rapidez.routing.controllers.product'), 'show']);
    Route::get('catalog/category/view/id/{categoryId}', [config('rapidez.routing.controllers.category'), 'show']);

    Route::view('cart', 'rapidez::cart.overview')->name('cart');
    Route::view('checkout', 'rapidez::checkout.overview')->name('checkout');
    Route::get('search', SearchController::class)->name('search');
    Route::fallback(FallbackController::class);
});
