@props(['query'])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">

    @if ($file = vite_filename_path('Listing.vue'))
        @vite([$file])
    @endif
@endPushOnce

<div class="min-h-screen">
    <listing
        :additional-filters="{!! isset($query) ? "['query-filter', 'category', 'score-position']" : "['category', 'score-position']" !!}"
        :additional-sorting="[{
            label: window.config.translations.newest,
            dataField: 'created_at',
            sortBy: 'desc'
        }]"
        v-cloak
    >
        <div slot-scope="{ loaded, filters, sortOptions, reactiveFilters, _renderProxy: listingSlotProps }">
            <x-rapidez::reactive-base v-if="loaded">
                @isset($query)
                    <reactive-component
                        component-id="query-filter"
                        :custom-query="function () {return {query: {{ $query }} } }"
                        :show-filter="false"
                    ></reactive-component>
                @endisset
                <reactive-component
                    component-id="score-position"
                    :custom-query="function () {
                        if (!window.config.category?.entity_id) {
                            return;
                        }

                        return {
                            query: {
                                function_score: {
                                    field_value_factor: {
                                        field: 'positions.'+window.config.category.entity_id,
                                        missing: 0
                                    }
                                }
                            }
                        }
                    }"
                    :show-filter="false"
                ></reactive-component>

                {{ $before ?? '' }}
                @if ($slot->isEmpty())
                    <div class="flex flex-col lg:flex-row gap-x-4">
                        <div class="xl:w-1/5">
                            @include('rapidez::listing.filters')
                        </div>
                        <div class="flex-1">
                            @include('rapidez::listing.products')
                        </div>
                    </div>
                @else
                     {{ $slot }}
                @endif
                {{ $after ?? '' }}
            </x-rapidez::reactive-base>
        </div>
    </listing>
</div>
