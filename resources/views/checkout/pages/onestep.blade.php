@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <div v-if="hasCart" v-cloak>
            <form class="grid md:grid-cols-2 gap-5" v-on:submit.prevent="(e) => {
                submitFieldsets(e.target?.form ?? e.target).then((result) => window.app.$emit('checkout-credentials-saved') && window.app.$emit('checkout-payment-saved') && window.app.$emit('placeOrder')).catch();
            }">
                <div class="md:col-span-2">
                    @include('rapidez::checkout.steps.login')
                </div>
                <div v-if="!cart.is_virtual">
                    <h2 class="text-xl font-bold mb-3">@lang('Shipping address')</h2>
                    <div class="md:h-[24px]">{{-- To keep the shipping and billing forms inline --}}</div>
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
    </div>
@endsection
