@extends('rapidez::layouts.app')

@section('title', 'Checkout')

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <checkout v-cloak v-slot="{ checkout, cart, hasItems, save, goToStep }">
            <div>
                <template v-if="checkout.step !== 4">
                    @include('rapidez::checkout.partials.progressbar')
                </template>
                <div v-if="checkout.step == 1 && hasItems">
                    @include('rapidez::checkout.steps.login')
                </div>

                <div v-if="[2, 3].includes(checkout.step)" class="lg:flex -mx-2">
                    <div class="w-full mb-5 lg:w-3/4 px-2">
                        <div v-if="checkout.step == 2">
                            @include('rapidez::checkout.steps.credentials')
                        </div>

                        <div v-if="checkout.step == 3">
                            @include('rapidez::checkout.steps.payment')
                        </div>
                    </div>
                    <div class="w-full lg:w-1/4 px-2 lg:mt-16">
                        @include('rapidez::checkout.partials.sidebar')
                    </div>
                </div>

                <div v-if="checkout.step == 4">
                    @include('rapidez::checkout.steps.success')
                </div>
            </div>
        </checkout>
    </div>
@endsection
