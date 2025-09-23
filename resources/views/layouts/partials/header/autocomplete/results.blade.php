
@include('rapidez::layouts.partials.header.autocomplete.no-results')

@foreach (config('rapidez.frontend.autocomplete.additionals') as $key => $fields)
    @includeIf('rapidez::layouts.partials.header.autocomplete.' . $key)
@endforeach

@includeWhen(config('rapidez.frontend.autocomplete.additionals.products') === null,'rapidez::layouts.partials.header.autocomplete.products')

@include('rapidez::layouts.partials.header.autocomplete.all-results')