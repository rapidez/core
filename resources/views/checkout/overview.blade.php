@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <checkout v-cloak v-slot="{ checkout, cart, hasItems, save, goToStep }">
            <div>
                <template v-if="checkout.step < getCheckoutStep('success')">
                    @include('rapidez::checkout.partials.progressbar')
                </template>
                <div v-if="checkout.step == 1 && hasItems">
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
        </checkout>
    </div>
@endsection
