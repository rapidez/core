@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <div v-if="hasCart" v-cloak>
            <form class="grid grid-cols-2 gap-5" v-on:submit.prevent="(e) => {
                submitFieldsets(e.target?.form ?? e.target).then((result) => window.app.$emit('placeOrder')).catch();
            }">
                <div class="col-span-2">
                    @include('rapidez::checkout.steps.login')
                </div>
                <div v-if="!cart.is_virtual">
                    <h2 class="text-xl font-bold">@lang('Shipping address')</h2>
                    @include('rapidez::checkout.steps.shipping_address')
                </div>
                <div>
                    <h2 class="text-xl font-bold">@lang('Billing address')</h2>
                    @include('rapidez::checkout.steps.billing_address')
                </div>
                <div v-if="!cart.is_virtual">
                    <h2 class="text-xl font-bold">@lang('Shipping method')</h2>
                    @include('rapidez::checkout.steps.shipping_method')
                </div>
                <div>
                    <h2 class="text-xl font-bold">@lang('Payment method')</h2>
                    @include('rapidez::checkout.steps.payment_method')
                </div>
                <div>
                    @include('rapidez::checkout.steps.place_order')
                </div>
            </form>
        </div>
    </div>
@endsection
