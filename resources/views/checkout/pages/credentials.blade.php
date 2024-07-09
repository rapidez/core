@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <div v-if="hasCart" v-cloak>
            <div class="flex gap-5">
                <div class="w-3/4">
                    <form
                        v-on:submit.prevent="(e) => {submitFieldsets(e.target?.form ?? e.target).then((result) => window.Turbo.visit(window.url('{{ route('checkout', ['step' => 'payment']) }}'))).catch();}"
                        class="flex flex-col gap-5">
                        <h2 class="text-xl font-bold">@lang('Shipping address')</h2>
                        @include('rapidez::checkout.steps.shipping_address')

                        <h2 class="text-xl font-bold">@lang('Billing address')</h2>
                        @include('rapidez::checkout.steps.billing_address')

                        <h2 class="text-xl font-bold">@lang('Shipping method')</h2>
                        @include('rapidez::checkout.steps.shipping_method')

                        <x-rapidez::button type="submit">
                            @lang('Next')
                        </x-rapidez::button>
                    </form>
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
