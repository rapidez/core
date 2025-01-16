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
        <div slot-scope="{ loaded, filters, sortOptions, reactiveFilters, getQuery, _renderProxy: listingSlotProps }">
            {{-- TODO: Implement $query and make sure the default product in category position is applied --}}
            <ais-instant-search
                v-if="loaded"
                :search-client="listingSlotProps.searchClient"
                index-name="products_1"
                :routing="listingSlotProps.routing"
            >
                {{-- TODO: This isn't working?  --}}
                <ais-configure :hitsPerPage="9" />
                {{ $before ?? '' }}
                @if ($slot->isEmpty())
                    <div class="flex flex-col lg:flex-row gap-x-6 gap-y-3">
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
            </ais-instant-search>
        </div>
    </listing>
</div>
