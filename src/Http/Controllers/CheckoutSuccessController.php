<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use TorMorten\Eventy\Facades\Eventy;

class CheckoutSuccessController
{
    public function __invoke(Request $request)
    {
        // This filter is responsible for checking successfull payment, and if it isn't re-enabling the cart.
        $success = Eventy::filter('checkout.checksuccess', true, $request);

        if (! $success) {
            return redirect('cart');
        }

        if (is_object($success) && $success instanceof \Symfony\Component\HttpFoundation\Response) {
            return $success;
        }

        return response(view('rapidez::checkout.pages.success'))->withCookie(cookie('mask', null, 0));
    }
}
