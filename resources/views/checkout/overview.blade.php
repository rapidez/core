@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <div v-if="hasCart" v-cloak>
            @include('rapidez::checkout.steps.email')
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <h2 class="text-xl font-bold">@lang('Shipping')</h2>
                    @include('rapidez::checkout.steps.shipping_address')
                </div>
                <div>
                    <h2 class="text-xl font-bold">@lang('Billing')</h2>
                    @include('rapidez::checkout.steps.billing_address')
                </div>
                <div>
                    <h2 class="text-xl font-bold">@lang('Method')</h2>
                    @include('rapidez::checkout.steps.shipping_method')
                </div>
                <div>
                    <h2 class="text-xl font-bold">@lang('Payment')</h2>
                    @include('rapidez::checkout.steps.payment_method')
                </div>
                <div>
                    @include('rapidez::checkout.steps.place_order')
                </div>
            </div>
        </div>
        {{--
        TODO: This isn't very nice but not sure yet if we could redirect
        from the CheckoutController when there is no quote yet.
        --}}
        <meta v-else :http-equiv="'refresh'" content="0; url=/">






        {{-- <checkout v-cloak v-slot="{ checkout, cart, save, goToStep, forceAccount }">
            <div>
                <template v-if="checkout.step < getCheckoutStep('success')">
                    @include('rapidez::checkout.partials.progressbar')
                </template>
                <div v-if="checkout.step == 1 && window.app.cart.total_quantity">
                    @include('rapidez::checkout.steps.login')
                </div>

                <div v-if="[2, 3].includes(checkout.step)" class="-mx-2 lg:flex">
                    <div class="mb-5 w-full px-2 lg:w-3/4">
                        <div v-if="checkout.step == 2">
                            @include('rapidez::checkout.steps.credentials')
                        </div>

                        <div v-if="checkout.step == 3">
                            @include('rapidez::checkout.steps.payment')
                        </div>
                    </div>
                    <div class="w-full px-2 lg:mt-16 lg:w-1/4">
                        @include('rapidez::checkout.partials.sidebar')
                    </div>
                </div>

                <div v-if="checkout.step == getCheckoutStep('success')">
                    @include('rapidez::checkout.steps.success')
                </div>
            </div>
        </checkout> --}}
    </div>
@endsection
