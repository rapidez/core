@props([
    'model' => 'addToCart.qty',
    'minSaleQty' => 1,
    'qtyIncrements' => 1,
])

<x-rapidez::select
    {{ $attributes->class('w-auto') }}
    name="qty"
    label="Quantity"
    v-model="{{ $model }}"
    labelClass="flex items-center sr-only mr-3"
    wrapperClass="flex"
>
    <option v-for="i in ({{ $qtyIncrements }} * 10)" v-if="i % {{ $qtyIncrements }} === 0" v-bind:value="i" v-text="i"></option>
</x-rapidez::select>
