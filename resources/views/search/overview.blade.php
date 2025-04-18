@extends('rapidez::layouts.app')

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container" v-cloak>
        <h1 class="font-bold text-3xl">
            @lang('Search')
            {{-- TODO: Re-add the search term reactive? --}}
        </h1>

        <x-rapidez::listing filter-query-string="visibility:(3 OR 4)"/>
    </div>
@endsection
