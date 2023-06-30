<add-to-cart :default-qty="{{ $product->min_sale_qty > $product->qty_increments ? $product->min_sale_qty : $product->qty_increments }}">
    <form slot-scope="{ self: addToCartSlotProps, options, customOptions, error, add, disabledOptions, simpleProduct, adding, added, price, specialPrice, setCustomOptionFile }" v-on:submit.prevent="add">
        <div class="flex items-center space-x-3 font-bold mb-3">
            <div class="text-3xl" v-text="$options.filters.price(specialPrice || price)">
                {{ price($product->special_price ?: $product->price) }}
            </div>
            <div class="line-through" v-if="specialPrice" v-text="$options.filters.price(price)">
                {{ $product->special_price ? price($product->price) : '' }}
            </div>
        </div>

        @if(!$product->in_stock)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            <div v-for="(superAttribute, superAttributeId) in config.product.super_attributes" v-cloak>
                <x-rapidez::label v-bind:for="'super_attribute_'+superAttributeId">@{{ superAttribute.label }}</x-rapidez::label>
                <x-rapidez::select
                    label=""
                    v-bind:id="'super_attribute_'+superAttributeId"
                    v-bind:name="superAttributeId"
                    v-model="options[superAttributeId]"
                    class="block w-64 mb-3"
                >
                    <option disabled selected hidden :value="undefined">@lang('Select') @{{ superAttribute.label.toLowerCase() }}</option>
                    <option
                        v-for="option in Object.values(config.product['super_'+superAttribute.code]).sort((a, b) => a.sort_order - b.sort_order)"
                        v-text="option.label"
                        :value="option.value"
                        :disabled="disabledOptions['super_'+superAttribute.code].includes(option.value)"
                    />
                </x-rapidez::select>
            </div>

            @include('rapidez::product.partials.options')

            <div class="flex mt-5">
                <x-rapidez::select
                    name="qty"
                    label="Quantity"
                    v-model="addToCartSlotProps.qty"
                    class="w-auto mr-3"
                    labelClass="flex items-center mr-3 sr-only"
                    wrapperClass="flex"
                >
                    @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </x-rapidez::select>

                <x-rapidez::button type="submit" class="flex items-center" dusk="add-to-cart">
                    <x-heroicon-o-shopping-cart class="h-5 w-5 mr-2" v-if="!adding && !added" />
                    <x-heroicon-o-refresh class="h-5 w-5 mr-2 animate-spin" v-if="adding" v-cloak />
                    <x-heroicon-o-check class="h-5 w-5 mr-2" v-if="added" v-cloak />
                    <span v-if="!adding && !added">@lang('Add to cart')</span>
                    <span v-if="adding" v-cloak>@lang('Adding')...</span>
                    <span v-if="added" v-cloak>@lang('Added')</span>
                </x-rapidez::button>
            </div>
        @endif
    </form>
</add-to-cart>
