<div class="flex flex-col" v-for="tierPrice in addToCart.tierPrices" :key="tierPrice.qty">
    <template v-if="tierPrice.qty > 1">
        <x-rapidez::input.radio
            name="tierPrice"
            v-on:click="addToCart.qty = tierPrice.qty"
            v-bind:checked="tierPrice === addToCart.currentTierPrice"
        >
            <span
                v-text="'@lang('Buy :qty for :price and save :percentage')'.replace(':qty', tierPrice.qty).replace(':price', window.price(tierPrice.value)).replace(':percentage', tierPrice.percentage_value + '%')"
            ></span>
        </x-rapidez::input.radio>
    </template>
</div>
<div class="contents" v-if="false">
    @php
        $tierPrices = $product->tierPrices
            ->filter(fn($tierPrice) => $tierPrice->all_groups && $tierPrice->qty > 1)
            ->map(function ($tierPrice) use ($product) {
                $tierPrice->percentage_value ??= ceil(100 - ($tierPrice->value * 100) / $product->price);
                $tierPrice->value = $tierPrice->value > 0 ? $tierPrice->value * 1 : $product->price - ($product->price * $tierPrice->percentage_value) / 100;
                return $tierPrice;
            });

        $tierPrices = $tierPrices->filter(fn($tierPrice) => !$tierPrices->contains(fn($ref) => $ref->qty <= $tierPrice->qty && $ref->value < $tierPrice->value))
    @endphp
    @foreach($tierPrices as $tierPrice)
        <div class="flex flex-col">
            <x-rapidez::input.radio
                name="tierPrice"
                v-on:click="addToCart.qty = {{$tierPrice->qty}}"
                v-bind:checked="tierPrice === addToCart.currentTierPrice"
            >
                <span>
                    @lang('Buy :qty for :price and save :percentage', ['qty' => $tierPrice->qty * 1, 'price' => price($tierPrice->value  * 1), 'percentage' => ($tierPrice->percentage_value * 1) . '%'])
                </span>
            </x-rapidez::input.radio>
        </div>
    @endforeach
</div>
