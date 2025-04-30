@props(['rootPath' => null])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @vite(vite_filename_paths(['Listing.vue', 'InstantSearch']))
@endPushOnce

<div class="min-h-screen">
    <listing
        {{ $attributes }}
        v-bind:index="config.index.product"
        v-slot="{ loaded, index, searchClient, rangeAttributes, categoryAttributes, hitsPerPage, filters, sortOptions, withFilters, withSwatches, routing }"
        v-cloak
    >
        <div>
            <ais-instant-search
                v-if="searchClient"
                :search-client="searchClient"
                :index-name="index"
                :routing="routing"
            >
                {{ $before ?? '' }}

                @slotdefault('slot')
                    <div class="flex flex-col lg:flex-row gap-x-12 gap-y-3">
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
</div>3
