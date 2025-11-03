
@include('rapidez::layouts.partials.header.autocomplete.no-results')

<div class="max-w-2xl w-full mx-auto sm:columns-2 *:break-inside-avoid space-y-3">
    @foreach (config('rapidez.frontend.autocomplete.additionals') as $key => $fields)
        @continue(in_array($key, ['popular-products', 'products']))
        @includeIf('rapidez::layouts.partials.header.autocomplete.' . $key)
    @endforeach
</div>

@include('rapidez::layouts.partials.header.autocomplete.popular-products')
@include('rapidez::layouts.partials.header.autocomplete.products')