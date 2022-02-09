<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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

    Route::get('cart/{quoteIdMaskOrCustomerToken}', function ($quoteIdMaskOrCustomerToken) {
        $quoteModel = config('rapidez.models.quote');

        return $quoteModel::where(function ($query) use ($quoteIdMaskOrCustomerToken) {
            $query->where('masked_id', $quoteIdMaskOrCustomerToken)
                  ->orWhere('token', $quoteIdMaskOrCustomerToken);
        })->firstOrFail();
    });

    Route::prefix('admin')->middleware(VerifyAdminToken::class)->group(function () {
        Route::match(['get', 'post'], 'cache/clear', fn () => Artisan::call('cache:clear'));
        Route::match(['get', 'post'], 'index/products', function (Request $request) {
            fastcgi_finish_request();
            Artisan::call('rapidez:index', [
                'store' => $request->store,
            ]);
        });
    });
});
