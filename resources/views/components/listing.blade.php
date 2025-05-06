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
