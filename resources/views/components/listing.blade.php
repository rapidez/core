@props(['query'])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @if($file = webpack_filename_with_chunkhash('js/listing.js'))
        <link rel="preload" href="/{{ $file }}" as="script">
    @endif
@endPushOnce

<div class="min-h-screen">
    <listing :additional-filters="{!! isset($query) ? "['query-filter', 'category']" : "['category']" !!}" v-cloak>
        <div slot-scope="{ loaded, filters, sortOptions, reactiveFilters }">
            <reactive-base
                :app="config.es_prefix + '_products_' + config.store"
                :url="config.es_url"
                v-if="loaded"
            >
                @isset($query)
                    <reactive-component component-id="query-filter" :show-filter="false">
                        <div slot-scope="{ setQuery }">
                            <query-filter :set-query="setQuery" :query="{{ $query }}"></query-filter>
                        </div>
                    </reactive-component>
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
