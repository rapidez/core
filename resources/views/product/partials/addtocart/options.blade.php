@if($product->options->isNotEmpty())
    <div class="flex flex-col space-y-3">
        @foreach($product->options->sortBy('sort_order') as $option)
            <div>
                @includeIf('rapidez::product.partials.addtocart.options.'.($option->type_id ?? $option->type))
            </div>
        @endforeach
    </div>
@endif
