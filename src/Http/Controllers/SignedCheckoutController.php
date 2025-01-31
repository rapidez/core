<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Models\Quote;

class SignedCheckoutController
{
    public function __invoke(Request $request)
    {
        $cache = Cache::store(config('rapidez.standalone_checkout.cache_store'));
        if (! $request->hasValidSignature() || ! $cache->has('checkout-' . $request->get('key'))) {
            return redirect(config('rapidez.magento_url'), 301);
        }

        $data = $cache->get('checkout-' . $request->get('key'));
        $cache->forget('checkout-' . $request->get('key'));

        $response = redirect()->to('checkout', 301);

        if (! Quote::whereQuoteIdOrCustomerToken($data['mask'] ?? $data['token'])->exists()) {
            return redirect(config('rapidez.magento_url'), 301);
        }

        $response->withCookie('mask', $data['mask'] ?? null, 525949, null, null, null, false);
        $response->withCookie('token', $data['token'] ?? null, 525949, null, null, null, false);

        return $response;
    }
}
