@props(['query'])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @if($file = vite_filename_with_chunkhash('Listing.vue'))
        <link rel="preload" href="/build/{{ $file }}" as="script">
    @endif
@endPushOnce

<div class="min-h-screen">
    <listing
        :additional-filters="{!! isset($query) ? "['query-filter', 'category']" : "['category']" !!}"
        :additional-sorting="[{
            label: window.config.translations.newest,
            dataField: 'created_at',
            sortBy: 'desc'
        }]"
        v-cloak
    >
        <div slot-scope="{ loaded, filters, sortOptions, reactiveFilters }">
            <reactive-base
                :app="config.es_prefix + '_products_' + config.store"
                :url="config.es_url"
                v-if="loaded"
            >
                @isset($query)
                    <reactive-component
                        component-id="query-filter"
                        :custom-query="function () {return {query: {{ $query }} } }"
                        :show-filter="false"
                    ></reactive-component>
                @endisset

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
