<div class="grid grid-cols-3 gap-3 grid-cols-[auto_max-content_max-content]">
    <template v-for="product in config.product.grouped">
        <add-to-cart :product="product" v-slot="addToCart" v-cloak>
            <form v-on:submit.prevent="addToCart.add" class="contents">
                <div>
                    @{{ addToCart.simpleProduct.name }}
                    <div class="flex items-center space-x-3 font-bold">
                        <div>@{{ (addToCart.simpleProduct.special_price || addToCart.simpleProduct.price) | price }}</div>
                        <div class="line-through" v-if="addToCart.simpleProduct.special_price">@{{ addToCart.simpleProduct.price | price }}</div>
                    </div>
                </div>

                <p class="col-span-2 self-center text-red-600" v-if="!addToCart.simpleProduct.in_stock">
                    @lang('Sorry! This product is currently out of stock.')
                </p>

                <template v-else>
                    <x-rapidez::select
                        name="qty"
                        label="Quantity"
                        v-model="addToCart.qty"
                        class="w-auto"
                        labelClass="flex items-center mr-3 sr-only"
                        wrapperClass="flex"
                    >
                        <option v-for="index in 10" :value="index * addToCart.simpleProduct.qty_increments">
                            @{{ index * addToCart.simpleProduct.qty_increments }}
                        </option>
                    </x-rapidez::select>

                    <x-rapidez::button.cart/>
                </template>
            </form>
        </add-to-cart>
    </template>
</div>
