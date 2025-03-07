@extends('rapidez::layouts.app')

@section('title', __('Search for').': '.request()->q)
@section('description', __('Search for').': '.request()->q)
@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <h1 class="font-bold text-3xl">@lang('Search for'): {{ request()->q }}</h1>

        <x-rapidez::listing filter-query-string="visibility:(3 OR 4)"/>
    </div>
@endsection
