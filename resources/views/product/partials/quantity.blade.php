<label class="flex items-center gap-3">
    <x-rapidez::label class="sr-only">@lang('Quantity')</x-rapidez::label>
    <x-rapidez::input.select name="qty" v-model="addToCart.qty" class="h-full">
        @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </x-rapidez::input.select>
</label>
