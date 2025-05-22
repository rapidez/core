@props(['rootPath' => null])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @vite(vite_filename_paths(['Listing.vue', 'InstantSearch']))
@endPushOnce

<div class="min-h-screen">
    <listing
        {{ $attributes }}
        v-slot="{ loaded, index, searchClient, rangeAttributes, categoryAttributes, hitsPerPage, filters, sortOptions, withFilters, withSwatches, routing, middlewares }"
        v-cloak
    >
        <div>
            <ais-instant-search
                v-if="searchClient"
                :search-client="searchClient"
                :middlewares="middlewares"
                :index-name="index"
                :routing="routing"
            >
                {{ $before ?? '' }}

                @slotdefault('slot')
                    <div class="flex gap-x-20 gap-y-5 max-lg:flex-col">
                        <div class="lg:w-80 shrink-0">
                            @include('rapidez::listing.filters')
                        </div>
                        <div class="flex-1">
                            {{ $title ?? '' }}

                            @include('rapidez::listing.products')
                        </div>
                    </div>
                @endslotdefault

                {{ $after ?? '' }}
            </ais-instant-search>
        </div>
    </listing>
</div>
