@extends('rapidez::layouts.app')

@section('title', 'Checkout')

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container mx-auto">
        <checkout v-cloak v-slot="{ checkout, cart, hasItems, save, goToStep }">
            <div>
                @include('rapidez::checkout.partials.progressbar')
                <div v-if="checkout.step == 1 && hasItems">
                    @include('rapidez::checkout.steps.login')
                </div>

                <div v-if="[2, 3].includes(checkout.step)" class="md:flex -mx-2">
                    <div class="w-full mb-5 md:w-2/3 lg:w-3/4 px-2">
                        <div v-if="checkout.step == 2">
                            @include('rapidez::checkout.steps.credentials')
                        </div>

                        <div v-if="checkout.step == 3">
                            @include('rapidez::checkout.steps.payment')
                        </div>
                    </div>
                    <div class="md:w-1/3 lg:w-1/4 px-2 md:mt-16">
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
