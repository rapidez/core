@props(['query'])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @if($file = webpack_filename_with_chunkhash('js/listing.js'))
        <link rel="preload" href="/{{ $file }}" as="script">
    @endif
@endPushOnce

<div class="min-h-screen">
    <listing
        :additional-filters="['query-filter', 'category']"
        :additional-sorting="[{
            label: window.config.translations.newest,
            dataField: 'created_at',
            sortBy: 'desc'
        }]"
        @isset($query)
            :query="{{ $query }}"
        @endisset
        v-cloak
    >
        <div slot-scope="{ loaded, filters, sortOptions, reactiveFilters, getQuery }">
            <reactive-base
                :app="config.es_prefix + '_products_' + config.store"
                :url="config.es_url"
                v-if="loaded"
            >
                <reactive-component component-id="query-filter" :custom-query="getQuery" :show-filter="false"></reactive-component>

                {{ $before ?? '' }}
                @if ($slot->isEmpty())
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/5">
                            @include('rapidez::listing.filters')
                        </div>
                        <div class="md:w-4/5">
                            @include('rapidez::listing.products')
                        </div>
                    </div>
                @else
                     {{ $slot }}
                @endif
                {{ $after ?? '' }}
            </reactive-base>
        </div>
    </listing>
</div>
