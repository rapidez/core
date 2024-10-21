@props([
    'model' => 'addToCart.qty',
    'minSaleQty' => 1,
    'qtyIncrements' => 1,
    'index' => 1,
])

<quantity-select :input-ref="'qty-select-' + {{ $index }}" :model="parseInt({{ $model }})" :min-qty="{{ $minSaleQty }}" :increment="{{ $qtyIncrements }}" v-slot="qtySelect">
    <div class="flex items-center justify-center border rounded bg-white h-14 self-start">
        <button
            @click.prevent="qtySelect.decrease"
            v-bind:disabled="qtySelect.model <= qtySelect.defaultQty"
            class="shrink-0 pl-2.5 text-neutral disabled:cursor-not-allowed disabled:text-neutral/50"
        >
            <x-heroicon-o-minus-small class="mt-0.5 size-5" stroke-width="2" />
        </button>
        <x-rapidez::input
            name="qty"
            type="number"
            :label="false"
            v-model="{{ $model }}"
            dusk="qty-select"
            v-bind:ref="'qty-select-' + {{ $index }}"
            class="border-none !w-12 bg-transparent font-medium text-center !px-0 sm:text-base"
            @change="(e) => qtySelect.updateQty(e.target.value)"
            :min="$minSaleQty"
            :step="$qtyIncrements"
        />
        <button
            @click.prevent="qtySelect.increase"
            class="shrink-0 pr-2.5 text-neutral"
        >
            <x-heroicon-o-plus class="mt-0.5 size-5" stroke-width="2" />
        </button>
    </div>
</quantity-select>