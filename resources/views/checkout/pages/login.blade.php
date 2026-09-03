@extends('rapidez::layouts.app')

@section('title', __('Checkout'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        @include('rapidez::checkout.partials.progressbar')
        <form
            v-if="hasCart"
            v-on:submit.prevent="(e) => {
                window.app.config.globalProperties.submitPartials(e.target?.form ?? e.target)
                    .then((result) =>
                        window.Turbo.visit(url('{{ route('checkout', ['step' => 'credentials']) }}'))
                    ).catch(() => {});
            }"
            class="max-w-md mx-auto"
            v-cloak
        >
            @include('rapidez::checkout.steps.login')

            <x-rapidez::button.conversion type="submit" data-testid="continue" class="mt-3" loader v-on:mousedown.prevent="{{-- Do not remove, this prevents requiring double click for the submit action --}}">
                @lang('Next')
            </x-rapidez::button.conversion>
        </form>
    </div>
@endsection
