@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <form v-if="hasCart" v-on:submit.prevent="(e) => {submitFieldsets(e.target?.form ?? e.target).then((result) => window.Turbo.visit(window.url('{{ route('checkout', ['step' => 'credentials']) }}'))).catch();}" v-cloak>
            @include('rapidez::checkout.steps.login')

            <x-rapidez::button type="submit" dusk="continue">
                @lang('Next')
            </x-rapidez::button>
        </form>
    </div>
@endsection