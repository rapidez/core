@foreach($product->options->sortBy('sort_order') as $option)
    @include('rapidez::product.partials.options.'.$option->type)
@endforeach

