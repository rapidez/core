@props(['rootPath' => null])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">

    @if ($file = vite_filename_path('Listing.vue'))
        @vite([$file])
    @endif
@endPushOnce

<div class="min-h-screen">
    <listing
        {{ $attributes }}
        index="{{ (new (config('rapidez.models.product')))->searchableAs() }}"
        inline-template
        v-cloak
    >
        <div>
            <ais-instant-search
                v-if="loaded"
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
