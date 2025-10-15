@props(['rootPath' => null])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @vite(vite_filename_paths(['Listing.vue', 'InstantSearch']))
@endPushOnce

<div class="min-h-screen">
    <listing
        {{ $attributes }}
        v-slot="listingSlotProps"
        v-cloak
        v-bind:root-path='@json($rootPath)'
    >
        <div>
            <ais-instant-search
                v-if="listingSlotProps.searchClient"
                :search-client="listingSlotProps.searchClient"
                :middlewares="listingSlotProps.middlewares"
                :index-name="listingSlotProps.index"
                :routing="listingSlotProps.routing"
            >
                {{ $before ?? '' }}

                @slotdefault('slot')
                    <div class="flex gap-x-20 gap-y-5 max-lg:flex-col min-h-screen">
                        <div class="lg:w-80 shrink-0" data-testid="listing-filters">
                            @include('rapidez::listing.filters')
                        </div>
                        <div class="flex-1" data-testid="listing-products">
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
