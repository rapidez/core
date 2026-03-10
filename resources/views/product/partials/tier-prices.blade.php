<template v-for="tierPrice in addToCart.tierPrices" :key="tierPrice.qty">
    <div class="flex flex-col" v-if="tierPrice.qty > 1">
        <x-rapidez::input.radio
            name="tierPrice"
            v-on:click="addToCart.qty = tierPrice.qty"
            v-bind:checked="addToCart.currentTierPrice?.value_id === tierPrice.value_id"
        >
            <span
                v-text="'@lang('Buy :qty for :price and save :percentage')'.replace(':qty', tierPrice.qty).replace(':price', window.price(tierPrice.value)).replace(':percentage', tierPrice.percentage_value + '%')"
            ></span>
        </x-rapidez::input.radio>
    </div>
</template>
<div class="contents" v-if="false">
    @foreach ($product->calculatedTierPrices as $tierPrice)
        <div class="flex flex-col">
            <x-rapidez::input.radio
                name="tierPrice"
                v-on:click="addToCart.qty = {{ $tierPrice->qty }}"
                v-bind:checked="addToCart.currentTierPrice?.value_id === tierPrice.value_id"
            >
                <span>
                    @lang('Buy :qty for :price and save :percentage', ['qty' => $tierPrice->qty * 1, 'price' => price($tierPrice->value  * 1), 'percentage' => ($tierPrice->percentage_value * 1) . '%'])
                </span>
            </x-rapidez::input.radio>
        </div>
    @endforeach
</div>
