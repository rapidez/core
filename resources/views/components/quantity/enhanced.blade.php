@props([
    'model' => 'addToCart.qty',
    'minSaleQty' => 1,
    'qtyIncrements' => 1,
    'index' => 1,
])

<enhanced-quantity-select :input-ref="'qty-select-' + {{ $index }}" :model="parseInt({{ $model }})" :min-qty="{{ $minSaleQty }}" :increment="{{ $qtyIncrements }}" v-slot="qtySelect">
    <div class="flex">
        <x-rapidez::button.outline @click.prevent="qtySelect.decrease">
            <div class="bg-neutral h-0.5 w-3 hover:bg-white"></div>
        </x-rapidez::button.outline>
        <x-rapidez::input
            name="qty"
            type="number"
            :label="false"
            v-model="{{ $model }}"
            dusk="qty-select"
            v-bind:ref="'qty-select-' + {{ $index }}"
            class="!w-14 !px-1 text-center"
            @change="(e) => qtySelect.updateQty(e.target.value)"
            :min="$minSaleQty"
            :step="$qtyIncrements"
        />
        <x-rapidez::button.outline @click.prevent="qtySelect.increase">
            <div class="relative">
                <div class="bg-neutral absolute h-0.5 w-3 rotate-90"></div>
                <div class="bg-neutral h-0.5 w-3"></div>
            </div>
        </x-rapidez::button.outline>
    </div>
</enhanced-quantity-select>