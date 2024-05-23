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
    </div>
@endsection
