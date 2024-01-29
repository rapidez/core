@if ($product->options->isNotEmpty())
    <div class="flex flex-col space-y-3">
        @foreach ($product->options->sortBy('sort_order') as $option)
            <div>
                @include('rapidez::product.partials.options.'.$option->type_id)
            </div>
        @endforeach
    </div>
@endif
