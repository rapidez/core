@props(['rootPath' => null])

@pushOnce('head', 'es_url-preconnect')
    <link rel="preconnect" href="{{ config('rapidez.es_url') }}">
    @vite(vite_filename_paths(['Listing.vue', 'InstantSearch', 'instantsearch-components']))
@endPushOnce

<div class="min-h-screen">
    @ssr('rapidez::listing.template')
</div>
