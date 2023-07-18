<div class="grid grid-cols-3 gap-3 grid-cols-[auto_max-content_max-content]">
    <template v-for="product in config.product.grouped">
        <add-to-cart :product="product" v-cloak>
            <div class="contents" slot-scope="{ self: addToCartSlotProps, add, simpleProduct, adding, added }">
                <div>
                    @{{ simpleProduct.name }}
                    <div class="flex items-center space-x-3 font-bold">
                        <div>@{{ (simpleProduct.special_price || simpleProduct.price) | price }}</div>
                        <div class="line-through" v-if="simpleProduct.special_price">@{{ simpleProduct.price | price }}</div>
                    </div>
                </div>

                <p class="col-span-2 self-center text-red-600" v-if="!simpleProduct.in_stock">
                    @lang('Sorry! This product is currently out of stock.')
                </p>

                <template v-else>
                    <x-rapidez::select
                        name="qty"
                        label="Quantity"
                        v-model="addToCartSlotProps.qty"
                        class="w-auto"
                        labelClass="flex items-center mr-3 sr-only"
                        wrapperClass="flex"
                    >
                        <option v-for="index in 10" :value="index * simpleProduct.qty_increments">
                            @{{ index * simpleProduct.qty_increments }}
                        </option>
                    </x-rapidez::select>

                    <x-rapidez::button class="flex items-center" v-on:click="add" dusk="add-to-cart">
                        <x-heroicon-o-shopping-cart class="h-5 w-5 mr-2" v-if="!adding && !added" />
                        <x-heroicon-o-refresh class="h-5 w-5 mr-2 animate-spin" v-if="adding" />
                        <x-heroicon-o-check class="h-5 w-5 mr-2" v-if="added" />
                        <span v-if="!adding && !added">@lang('Add to cart')</span>
                        <span v-if="adding">@lang('Adding')...</span>
                        <span v-if="added">@lang('Added')</span>
                    </x-rapidez::button>
                </template>
            </div>
        </add-to-cart>
    </template>
</div>
