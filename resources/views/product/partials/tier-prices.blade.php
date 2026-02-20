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
