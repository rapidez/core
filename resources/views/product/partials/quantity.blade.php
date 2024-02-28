<x-rapidez::select
    name="qty"
    label="Quantity"
    v-model="addToCart.qty"
    class="w-auto"
    labelClass="flex items-center sr-only mr-3"
    wrapperClass="flex"
>
    @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
        <option value="{{ $i }}">{{ $i }}</option>
    @endfor
</x-rapidez::select>
