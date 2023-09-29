<?php

use Rapidez\Core\Http\Controllers\FallbackController;
use Rapidez\Core\Http\Controllers\SearchController;
use Rapidez\Core\Http\Controllers\HealthcheckController;
use Rapidez\Core\Http\Middleware\AuthenticateHealthCheck;

Route::get('healthcheck', HealthcheckController::class)->middleware(AuthenticateHealthCheck::class);

Route::middleware('web')->group(function () {
    Route::view('cart', 'rapidez::cart.overview')->name('cart');
    Route::view('checkout', 'rapidez::checkout.overview')->name('checkout');
    Route::get('search', [SearchController::class, '__invoke'])->name('search');
    Route::fallback(FallbackController::class);
});
