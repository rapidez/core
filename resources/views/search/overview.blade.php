@extends('rapidez::layouts.app')

@section('title', __('Search for').': '.request()->q)
@section('description', __('Search for').': '.request()->q)
@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <h1 class="font-bold text-3xl">@lang('Search for'): {{ request()->q }}</h1>

        <x-rapidez::listing
            v-bind:base-filters="() => [{ query_string: { filters: 'visibility:(3 OR 4)' }}]"
            v-bind:query="(query, searchAttributes, config) => {
                return {
                    bool: {
                        should: [{
                            multi_match: {
                                query: query,
                                fields: searchAttributes.map((value) => value.field+'^'+value.weight),
                                type: 'best_fields',
                                operator: 'or',
                                fuzziness: 'AUTO',
                            },
                        },
                        {
                            multi_match: {
                                query: query,
                                fields: searchAttributes.map((value) => value.field+'^'+value.weight),
                                type: 'phrase',
                                operator: 'or',
                            }
                        },
                        {
                            multi_match: {
                                query: query,
                                fields: searchAttributes.map((value) => value.field+'^'+value.weight),
                                type: 'phrase_prefix',
                                operator: 'or',
                            }
                        }]
                    }
                }
            }"
        />
    </div>
@endsection
