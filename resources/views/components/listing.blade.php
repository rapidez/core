@props(['rootPath' => null])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @vite(vite_filename_paths(['Listing.vue', 'InstantSearch']))
@endPushOnce

<div class="min-h-screen">
    <listing
        {{ $attributes }}
        :additional-sorting="[{
            label: window.config.translations.newest,
            field: 'created_at',
            order: 'desc',
            {{-- TODO: Extract this somewhere? --}}
            value: config.index_prefix + '_products_' + config.store + '_created_at_desc',
            key: '_created_at_desc'
        }]"
        {{-- TODO: Extract this somewhere? --}}
        :index="config.index_prefix + '_product_' + config.store"
        inline-template
        v-cloak
    >
        <div>
            <ais-instant-search
                v-if="loaded && searchClient"
                :search-client="searchClient"
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
