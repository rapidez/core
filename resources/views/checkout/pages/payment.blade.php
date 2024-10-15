@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        @include('rapidez::checkout.partials.progressbar')
        <div v-if="hasCart" v-cloak>
            <div class="flex gap-5 max-xl:flex-col">
                <form class="w-full xl:w-3/4 rounded bg-highlight h-fit p-4 md:p-8" v-on:submit.prevent="(e) => {
                        submitFieldsets(e.target?.form ?? e.target)
                            .then((result) =>
                                window.app.$emit('checkout-payment-saved')
                                && window.app.$emit('placeOrder')
                            ).catch();
                    }">
                    @include('rapidez::checkout.steps.payment_method')
                    @include('rapidez::checkout.steps.place_order')
                </form>
                <div class="w-full xl:w-1/4">
                    @include('rapidez::checkout.partials.sidebar')
                </div>
            </div>
        </div>
    </div>
@endsection
