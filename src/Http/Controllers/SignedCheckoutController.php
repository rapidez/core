<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Models\Quote;

class SignedCheckoutController
{
    public function __invoke(Request $request)
    {
        if (! $request->hasValidSignature() || !Cache::has('checkout-' . $request->get('key'))) {
            return redirect(config('rapidez.magento_url'), 301);
        }

        $data = Cache::get('checkout-' . $request->get('key'));
        Cache::forget('checkout-' . $request->get('key'));

        $response = redirect()->to('checkout');

        if (!Quote::whereQuoteIdOrCustomerToken($data['mask'] ?? $data['token'])->exists()) {
            return redirect(config('rapidez.magento_url'), 301);
        }

        if ($data['mask'] ?? false) {
            $response->withCookie('mask', $data['mask'], 525949, null, null, null, false);
        }

        if ($data['token'] ?? false) {
            $response->withCookie('token', $data['token'], 525949, null, null, null, false);
        }

        return $response;
    }
}
