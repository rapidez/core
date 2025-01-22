<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Rapidez\Core\Http\Controllers\GetSignedCheckoutController;
use Rapidez\Core\Http\Controllers\OrderController;
use Rapidez\Core\Http\Middleware\VerifyAdminToken;

Route::middleware('api')->prefix('api')->group(function () {
    Route::get('attributes', function () {
        $attributeModel = config('rapidez.models.attribute');

        return $attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['filter'] || $attribute['sorting'];
        });
    });

    Route::get('swatches', function () {
        $optionswatchModel = config('rapidez.models.option_swatch');

        return $optionswatchModel::getCachedSwatchValues();
    });

    Route::get('order', OrderController::class);

    Route::post('get-checkout-url', GetSignedCheckoutController::class);

    Route::prefix('admin')->middleware(VerifyAdminToken::class)->group(function () {
        Route::match(['get', 'post'], 'cache/clear', fn () => Artisan::call('cache:clear'));
        Route::match(['get', 'post'], 'index/products', function (Request $request) {
            fastcgi_finish_request();
            Artisan::call('rapidez:index', [
                'store' => $request->store ?: false,
            ]);
        });
    });
});
