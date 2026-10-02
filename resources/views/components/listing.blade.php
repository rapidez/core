@props(['rootPath' => null, 'ssr' => null])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @vite(vite_filename_paths(['Listing.vue', 'InstantSearch', 'instantsearch-components']))
@endPushOnce

<div class="min-h-screen">
    @if ($ssr)
        <listing-snapshot>
            <div data-testid="listing-ssr">
                <div class="flex gap-x-20 gap-y-5 max-lg:flex-col min-h-screen" v-pre>
                    {!! $ssr !!}
                </div>
            </div>
        </listing-snapshot>
    @endif

    <listing
        {{ $attributes }}
        v-slot="listingSlotProps"
        v-cloak
        v-bind:root-path='@json($rootPath)'
        @if ($ssr) v-bind:has-snapshot="true" @endif
    >
        <div ref="root">
            <ais-instant-search
                v-if="listingSlotProps.searchClient"
                :future="{ preserveSharedStateOnUnmount: true }"
                :search-client="listingSlotProps.searchClient"
                :middlewares="listingSlotProps.middlewares"
                :index-name="listingSlotProps.index"
                :routing="listingSlotProps.routing"
            >
                {{ $before ?? '' }}

                @slotdefault('slot')
                    <div
                        id="listing-content"
                        v-show="listingSlotProps.rendered"
                        v-bind:data-listing-loaded="listingSlotProps.loaded || null"
                        class="flex gap-x-20 gap-y-5 max-lg:flex-col min-h-screen"
                        ref="root"
                    >
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
