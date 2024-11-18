@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <div v-if="hasCart" class="flex gap-5 max-xl:flex-col" v-cloak>
            <div class="w-full bg-neutral-100 rounded p-4 xl:p-8 xl:w-3/4">
                <form class="grid gap-5 lg:grid-cols-2" v-on:submit.prevent="(e) => {
                    submitPartials(e.target?.form ?? e.target)
                        .then((result) =>
                            window.app.$emit('checkout-credentials-saved')
                            && window.app.$emit('checkout-payment-saved')
                            && window.app.$emit('placeOrder')
                        ).catch();
                }">
                    <div class="lg:w-1/2 lg:pr-2.5 lg:col-span-2">
                        @include('rapidez::checkout.steps.login')
                    </div>
                    <div v-if="!cart.is_virtual">
                        <h2 class="text-xl font-bold mb-3 lg:mb-9">@lang('Shipping address')</h2>
                        @include('rapidez::checkout.steps.shipping_address')
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mb-3">@lang('Billing address')</h2>
                        @include('rapidez::checkout.steps.billing_address')
                    </div>
                    <div v-if="!cart.is_virtual">
                        <h2 class="text-xl font-bold mb-3">@lang('Shipping method')</h2>
                        @include('rapidez::checkout.steps.shipping_method')
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mb-3">@lang('Payment method')</h2>
                        @include('rapidez::checkout.steps.payment_method')
                    </div>
                    <div>
                        @include('rapidez::checkout.steps.place_order')
                    </div>
                </form>
            </div>
            <div class="w-full xl:w-1/4">
                @include('rapidez::checkout.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
