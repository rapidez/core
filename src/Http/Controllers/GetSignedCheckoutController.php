<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class GetSignedCheckoutController
{
    /**
     * Time for the signed route to be valid for, 2 minutes will result in a timeout anyways.
     * So it's better to remove the mask and token after that time.
     */
    public const URL_TIMEOUT = 120;

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'mask'  => 'required',
            'token' => 'nullable',
        ]);
        $cachekey = (string) Str::uuid();
        Cache::put('checkout-' . $cachekey, $data, static::URL_TIMEOUT);

        return ['url' => URL::signedRoute('signed-checkout', ['key' => $cachekey], static::URL_TIMEOUT)];
    }
}
