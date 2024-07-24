@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <div v-if="hasCart" v-cloak>
            <div class="flex gap-5">
                <form class="w-3/4" v-on:submit.prevent="(e) => {submitFieldsets(e.target?.form ?? e.target).then((result) => window.app.$emit('placeOrder')).catch();}">
                    @include('rapidez::checkout.steps.payment_method')
                    @include('rapidez::checkout.steps.place_order')
                </form>
                <div class="w-1/4">
                    @include('rapidez::checkout.partials.sidebar')
                </div>
            </div>
        </div>
    </div>
@endsection
