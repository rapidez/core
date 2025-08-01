@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        @include('rapidez::checkout.partials.progressbar')
        <div v-if="hasCart" v-cloak>
            <div class="flex gap-5 max-xl:flex-col">
                <div class="w-full rounded bg p-4 xl:p-8 xl:w-3/4">
                    <form
                        v-on:submit.prevent="(e) => {
                            submitPartials(e.target?.form ?? e.target, (cart?.billing_address?.same_as_shipping ?? true))
                                .then((result) =>
                                    window.app.$emit('checkout-credentials-saved')
                                    && window.Turbo.visit(window.url('{{ route('checkout', ['step' => 'payment']) }}'))
                                ).catch();
                        }"
                        class="flex flex-col gap-5"
                    >
                        <template v-if="!cart.is_virtual">
                            <h2 class="text-xl font-bold">@lang('Shipping address')</h2>
                            @include('rapidez::checkout.steps.shipping_address')
                        </template>

                        <h2 class="text-xl font-bold">@lang('Billing address')</h2>
                        @include('rapidez::checkout.steps.billing_address')

                        <template v-if="!cart.is_virtual">
                            <h2 class="text-xl font-bold">@lang('Shipping method')</h2>
                            @include('rapidez::checkout.steps.shipping_method')
                        </template>

                        <x-rapidez::button.conversion type="submit" data-testid="continue" class="self-start">
                            @lang('Next')
                        </x-rapidez::button.conversion>
                    </form>
                </div>
                <div class="w-full xl:w-1/4">
                    @include('rapidez::checkout.partials.sidebar')
                </div>
            </div>
        </div>
    </div>
@endsection
