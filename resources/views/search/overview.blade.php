@extends('rapidez::layouts.app')

@section('title', __('Search for').': '.request()->q)
@section('description', __('Search for').': '.request()->q)

@section('content')
    <h1 class="font-bold text-3xl">@lang('Search for'): {{ request()->q }}</h1>

    <category :translations="{ relevance: '@lang('Relevance')', asc: '@lang('asc')', desc: '@lang('desc')' }" v-cloak>
        <div
            slot-scope="{ loaded, baseStyles, filters, reactiveFilters, sortOptions, categoryQuery }"
            :style="baseStyles"
        >
            <reactive-base
                :app="config.es_prefix + '_products_' + config.store"
                :url="config.es_url"
                v-if="loaded"
            >
                <selected-filters></selected-filters>
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/5">
                        @include('rapidez::category.partials.filters')
                    </div>
                    <div class="md:w-4/5">
                        @include('rapidez::category.partials.listing')
                    </div>
                </div>
            </reactive-base>
        </div>
    </category>
@endsection
