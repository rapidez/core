<?php

use Illuminate\Support\Facades\Route;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Http\Controllers\ConfigController;
use Rapidez\Core\Http\Controllers\SignedCheckoutController;
use Rapidez\Core\Http\Middleware\AuthenticateHealthCheck;

Route::get('healthcheck', config('rapidez.routing.controllers.healthcheck'))->middleware(AuthenticateHealthCheck::class);
Route::get('robots.txt', fn () => response(Rapidez::config('design/search_engine_robots/custom_instructions'))
    ->header('Content-Type', 'text/plain; charset=UTF-8'));
Route::middleware('cache.headers:public;max_age=3600;s_maxage=3600;stale_while_revalidate=3600;etag')->group(function () {
    Route::get('config.js', ConfigController::class)->name('config');
});

Route::middleware('web')->group(function () {
    Route::view('cart', 'rapidez::cart.overview')->name('cart');
    Route::get('checkout/success', config('rapidez.routing.controllers.checkout-success'))->name('checkout.success');

    Route::get('checkout/onepage/success', fn () => redirect(route('checkout.success', request()->query()), 308));
    Route::get('checkout/signed', SignedCheckoutController::class)->name('signed-checkout');
    Route::get('checkout/{step?}', config('rapidez.routing.controllers.checkout'))->middleware('auth:magento-cart')->name('checkout');
    Route::get('search', config('rapidez.routing.controllers.search'))->name('search');
    Route::fallback(config('rapidez.routing.controllers.fallback'));
});
