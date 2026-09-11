@props(['rootPath' => null, 'ssr' => null])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @vite(vite_filename_paths(['Listing.vue', 'InstantSearch', 'instantsearch-components']))
@endPushOnce

<div class="min-h-screen">
    {{-- Static snapshot of the last time this listing was captured (see
         GenerateCategoryListingSnapshot) - a literal copy of #listing-content's own
         rendered markup below, shown until the real, interactive listing has results
         (see the `listing:rendered` event dispatched from Listing.vue), so filters
         and products are visible before Vue and vue-instantsearch have booted. --}}
    @if ($ssr)
        <div id="listing-ssr" data-testid="listing-ssr">
            <div class="flex gap-x-20 gap-y-5 max-lg:flex-col min-h-screen">
                {!! $ssr !!}
            </div>
        </div>
        <script>
            {{-- The captured markup includes real forms/buttons (add to cart, ...) that
                 aren't wired up to anything until Vue mounts; without this, clicking one
                 would fall back to the browser's native (and useless) form submission. --}}
            document.getElementById('listing-ssr')?.addEventListener('submit', function (event) {
                event.preventDefault()
            })

            document.addEventListener('listing:rendered', function once() {
                document.removeEventListener('listing:rendered', once)
                document.getElementById('listing-ssr')?.remove()
            })
        </script>
    @endif

    <listing
        {{ $attributes }}
        v-slot="listingSlotProps"
        v-cloak
        v-bind:root-path='@json($rootPath)'
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
                    {{-- id is the capture target for GenerateCategoryListingSnapshot; keep its
                         direct children as just the filters/products so the snapshot swaps in
                         without a layout shift. `v-show` (not `v-if`) keeps the widgets inside
                         mounted so they can actually run their search - it just keeps this
                         hidden until they have real results, instead of briefly showing empty
                         filters/a "no results" state while the SSR snapshot is still visible. --}}
                    <div id="listing-content" v-show="listingSlotProps.rendered" class="flex gap-x-20 gap-y-5 max-lg:flex-col min-h-screen" ref="root">
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
