@extends('rapidez::layouts.app')

@section('robots', 'NOINDEX,NOFOLLOW')

@pushOnce('head', 'search-overview')
    @vite(vite_filename_paths(['StateResults.vue']))
@endPushOnce

@section('content')
    <div class="container">
        <x-rapidez::listing use-search-title filter-query-string="(visibility:(3 OR 4) OR (NOT _exists_:visibility))">
            <x-slot:before>
                <ais-state-results>
                    <template v-slot="{ state: { query } }">
                        <h1 class="font-medium text-2xl mb-5">
                            <template v-if="query">
                                @lang('Search for'): @{{ query }}
                            </template>
                            <template v-else>
                                @lang('Search')
                            </template>
                        </h1>
                    </template>
                </ais-state-results>
            </x-slot:before>
        </x-rapidez::listing>
    </div>
@endsection
