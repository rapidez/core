@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">

    @if ($file = vite_filename_path('Listing.vue'))
        @vite([$file])
    @endif
@endPushOnce

<div class="min-h-screen">
    <listing
        {{ $attributes }}
        :additional-sorting="[{
            label: window.config.translations.newest,
            field: 'created_at',
            order: 'desc',
            {{-- TODO: Extract this somewhere? --}}
            value: config.index_prefix + '_{{ config('rapidez.models.product')::indexName() }}_' + config.store + '_created_at_desc',
            key: '_created_at_desc'
        }]"
        index-model="{{ config('rapidez.models.product')::indexName() }}"
        v-cloak
    >
        <div slot-scope="{ loaded, filters, sortOptions, withFilters, withSwatches, filterPrefix, _renderProxy: listingSlotProps }">
            <ais-instant-search
                v-if="loaded"
                :search-client="listingSlotProps.searchClient"
                :index-name="listingSlotProps.index"
                :routing="listingSlotProps.routing"
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
