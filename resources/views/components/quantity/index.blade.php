@props([
    'model' => 'addToCart.qty',
    'minSaleQty' => 1,
    'qtyIncrements' => 1,
    'multiplier' => 10,
])

<x-rapidez::select
    {{ $attributes->class('w-auto') }}
    name="qty"
    label="Quantity"
    v-model="{{ $model }}"
    dusk="qty"
    labelClass="flex items-center sr-only mr-3"
    wrapperClass="flex"
>
    <option v-for="i in ({{ $qtyIncrements }} * {{ $multiplier }})" v-if="i % {{ $qtyIncrements }} === 0" v-bind:value="i">
        @{{ i }}
    </option>
</x-rapidez::select>
