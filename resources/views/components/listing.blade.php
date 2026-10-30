@props(['rootPath' => null, 'initialItems' => []])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @vite(vite_filename_paths(['Listing.vue', 'InstantSearch', 'instantsearch-components']))
@endPushOnce

<div class="min-h-screen">
    <listing-shell v-cloak v-bind:initial-items='@json($initialItems)' v-slot="{ items, sendEvent, setLive }">
        <div v-if="items.length" class="flex gap-x-20 gap-y-5 max-lg:flex-col min-h-screen" data-testid="listing-products-preview">
            <div class="lg:w-80 shrink-0">
                <x-rapidez::button.secondary for="category-filters-slideover" class="w-full lg:hidden">
                    @lang('Filters')
                </x-rapidez::button.secondary>
            </div>
            <div class="flex-1">
                <div class="flex flex-col max-lg:flex-wrap">
                    <div class="pb-4 border-b">
                        <div class="flex gap-1 sm:items-center text-sm">&nbsp;</div>
                    </div>
                    @include('rapidez::listing.partials.products-grid')
                </div>
            </div>
        </div>

        <div v-show="!items.length">
            <listing
                {{ $attributes }}
                v-slot="listingSlotProps"
                v-cloak
                v-bind:root-path='@json($rootPath)'
                v-on:request-complete="setLive"
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
                            <div class="flex gap-x-20 gap-y-5 max-lg:flex-col min-h-screen" ref="root">
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
    </listing-shell>
</div>
