@if ($product->options->isNotEmpty())
    <div class="flex flex-col space-y-3">
        @foreach ($product->options->sortBy('sort_order') as $option)
            <div>
                @includeFirstSafe(['rapidez::product.partials.options.'.$option->type])
            </div>
        @endforeach
    </div>
@endif
