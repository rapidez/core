<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController
{
    public function __invoke(Request $request, ?string $step = null)
    {
        $checkoutSteps = config('rapidez.frontend.checkout_steps.' . config('rapidez.store_code'))
            ?: config('rapidez.frontend.checkout_steps.default');

        if (! $step) {
            $step = $checkoutSteps[0];
        }

        abort_if(! in_array($step, $checkoutSteps), 404);

        return view('rapidez::checkout.pages.' . $step, [
            'checkoutSteps'  => $checkoutSteps,
            'currentStep'    => $step,
            'currentStepKey' => array_search($step, $checkoutSteps),
        ]);
    }
}
