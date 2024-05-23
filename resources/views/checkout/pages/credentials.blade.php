@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <div v-if="hasCart" v-cloak>
            <div class="flex gap-5">
                <div class="w-3/4">
                    @include('rapidez::checkout.steps.shipping_address')
                    @include('rapidez::checkout.steps.billing_address')
                    @include('rapidez::checkout.steps.shipping_method')

                    {{--
                    TODO: Run:
                    - setShippingAddressesOnCart
                    - setBillingAddressOnCart
                    With something like: `this.$root.$emit('eventName')`
                    --}}
                    <x-rapidez::button href="{{ route('checkout', ['step' => 'payment']) }}">
                        @lang('Next')
                    </x-rapidez::button>
                </div>
                <div class="w-1/4">
                    @include('rapidez::checkout.partials.sidebar')
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
