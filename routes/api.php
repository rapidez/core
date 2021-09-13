<?php

use Rapidez\Core\Http\Middleware\VerifyAdminToken;

Route::middleware('api')->prefix('api')->group(function () {
    Route::get('attributes', function () {
        $attributeModel = config('rapidez.models.attribute');

        return $attributeModel::getCachedWhere(function ($attribute) {
            return $attribute['filter'] || $attribute['sorting'];
        });
    });

    Route::get('swatches', function () {
        $optionswatchModel = config('rapidez.models.optionswatch');

        return $optionswatchModel::getCachedSwatchValues();
    });

    Route::prefix('admin')->middleware(VerifyAdminToken::class)->group(function () {
        Route::get('cache/clear', fn () => Artisan::call('cache:clear'));
        Route::get('index/products', function () {
            fastcgi_finish_request();
            Artisan::call('rapidez:index', [
                'store' => $request->store,
            ]);
        });
    });
});
