<x-rapidez::input.select
    name="qty"
    label="Quantity"
    v-model="addToCart.qty"
    class="w-20"
>
    @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
        <option value="{{ $i }}">{{ $i }}</option>
    @endfor
</x-rapidez::input.select>
