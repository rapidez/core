<x-rapidez::input.label class="sr-only">@lang('Quantity')</x-rapidez::input.label>
<x-rapidez::input.select name="qty" v-model="addToCart.qty" class="flex flex-1">
    @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
        <option value="{{ $i }}">{{ $i }}</option>
    @endfor
</x-rapidez::input.select>
