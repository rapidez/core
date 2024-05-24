@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <form v-if="hasCart" v-on:submit.prevent="() => {
            // Not sure yet if this is the best idea but seems like
            // it gives a lot of flexibility on how you arrange
            // all checkout steps. But.. no validation; yet.
            // How can we know the event was successful?
            window.app.$emit('setGuestEmailOnCart');
            window.Turbo.visit(window.url('{{ route('checkout', ['step' => 'credentials']) }}'));
        }" v-cloak>
            @include('rapidez::checkout.steps.email')

            <x-rapidez::button type="submit">
                @lang('Next')
            </x-rapidez::button>
        </form>
        {{--
        TODO: This isn't very nice but not sure yet if we could redirect
        from the CheckoutController when there is no quote yet.
        --}}
        <meta v-else :http-equiv="'refresh'" content="0; url=/">
    </div>
@endsection
