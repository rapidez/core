@extends('rapidez::layouts.app')

@section('title', __('Search for').': '.request()->q)
@section('description', __('Search for').': '.request()->q)

@section('content')
    <h1 class="font-bold text-3xl">@lang('Search for'): {{ request()->q }}</h1>

    <x-rapidez::listing query="{
        bool: {
            should: [
                {
                    multi_match: {
                        query: '{{ request()->q }}',
                        fields: Object.keys(config.searchable),
                        type: 'best_fields',
                        operator: 'or',
                        fuzziness: 'AUTO',
                    },
                },
                {
                    multi_match: {
                        query: '{{ request()->q }}',
                        fields: Object.keys(config.searchable),
                        type: 'phrase',
                        operator: 'or',
                    }
                }
            ]
        }
    }"/>
@endsection
