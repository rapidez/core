<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController
{
    public function __invoke(Request $request, ?string $step = null)
    {
        $checkoutSteps = config('rapidez.frontend.checkout_steps');

        if (! $step) {
            $step = $checkoutSteps[0];
        }

        if ($step === 'login' && auth('magento-customer')->user()) {
            $step = $checkoutSteps[array_search($step, $checkoutSteps) + 1];
        }

        abort_if(! in_array($step, $checkoutSteps), 404);

        if (! auth('magento-cart')->user()?->items_count) {
            return redirect()->route('cart');
        }

        return view('rapidez::checkout.pages.' . $step, [
            'checkoutSteps'  => $checkoutSteps,
            'currentStep'    => $step,
            'currentStepKey' => array_search($step, $checkoutSteps),
        ]);
    }
}
