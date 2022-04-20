<add-to-cart :default-qty="{{ $product->qty_increments }}" v-cloak>
    <div slot-scope="{ qty, changeQty, options, error, add, disabledOptions, simpleProduct, adding, added }">
        <div class="flex items-center space-x-3 font-bold mb-3">
            <div class="text-3xl">@{{ (simpleProduct.special_price || simpleProduct.price) | price }}</div>
            <div class="line-through" v-if="simpleProduct.special_price">@{{ simpleProduct.price | price }}</div>
        </div>

        @if(!$product->in_stock)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            <div v-for="(superAttribute, superAttributeId) in config.product.super_attributes">
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
                        v-for="option in config.product[superAttribute.code]"
                        v-text="option.label"
                        :value="option.value"
                        :disabled="disabledOptions[superAttribute.code].includes(option.value)"
                    />
                </x-rapidez::select>
            </div>

            <div class="flex mt-5">
                <x-rapidez::select
                    name="qty"
                    label="Quantity"
                    v-bind:value="qty"
                    v-on:input="changeQty"
                    class="w-auto mr-3"
                    labelClass="flex items-center mr-3 sr-only"
                    wrapperClass="flex"
                >
                    @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </x-rapidez::select>

                <x-rapidez::button class="flex items-center" v-on:click="add" dusk="add-to-cart">
                    <x-heroicon-o-shopping-cart class="h-5 w-5 mr-2" v-if="!adding && !added" />
                    <x-heroicon-o-refresh class="h-5 w-5 mr-2 animate-spin" v-if="adding" />
                    <x-heroicon-o-check class="h-5 w-5 mr-2" v-if="added" />
                    <span v-if="!adding && !added">@lang('Add to cart')</span>
                    <span v-if="adding">@lang('Adding')...</span>
                    <span v-if="added">@lang('Added')</span>
                </x-rapidez::button>
            </div>
        @endif
    </div>
</add-to-cart>
