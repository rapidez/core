@props(['filters' => null])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">

    @if ($file = vite_filename_path('Listing.vue'))
        @vite([$file])
    @endif
@endPushOnce

<div class="min-h-screen">
    <listing
        {{ $attributes }}
        :index="config.index_prefix + '_products_' + config.store"
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

                {{-- NOTE: Do not put this component above the filters if you want routing to work. --}}
                @if ($filters)
                    <ais-configure :filters="{!! $filters !!}"/>
                @endif
            </ais-instant-search>
        </div>
    </listing>
</div>
