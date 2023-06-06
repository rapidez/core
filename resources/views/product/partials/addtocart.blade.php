<add-to-cart :default-qty="{{ $product->min_sale_qty > $product->qty_increments ? $product->min_sale_qty : $product->qty_increments }}">
    <div slot-scope="{ qty, changeQty, options, error, add, disabledOptions, simpleProduct, adding, added }">
        <h1 class="mb-3 text-3xl font-bold" itemprop="name">{{ $product->name }}</h1>
        @if (!$product->in_stock)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            <div v-cloak v-for="(superAttribute, superAttributeId) in config.product.super_attributes">
                <x-rapidez::label v-bind:for="'super_attribute_'+superAttributeId">@{{ superAttribute.label }}</x-rapidez::label>
                <x-rapidez::select label="" v-bind:id="'super_attribute_'+superAttributeId" v-bind:name="superAttributeId" v-model="options[superAttributeId]" class="mb-3 block w-64">
                    <option disabled selected hidden :value="undefined">@lang('Select') @{{ superAttribute.label.toLowerCase() }}</option>
                    <option
                        v-for="option in Object.values(config.product['super_'+superAttribute.code]).sort((a, b) => a.sort_order - b.sort_order)"
                        v-text="option.label"
                        :value="option.value"
                        :disabled="disabledOptions['super_'+superAttribute.code].includes(option.value)"
                    />
                </x-rapidez::select>
            </div>
            <div class="mt-5 flex flex-wrap items-center gap-3" v-cloak>
                <div>
                    <div class="text-2xl font-bold text-primary">@{{ (simpleProduct.special_price || simpleProduct.price) | price }}</div>
                    <div class="text-lg text-gray-500 line-through" v-if="simpleProduct.special_price">@{{ simpleProduct.price | price }}</div>
                </div>
                <x-rapidez::select
                    class="w-auto"
                    name="qty"
                    label="Quantity"
                    v-bind:value="qty"
                    v-on:input="changeQty"
                    labelClass="flex items-center sr-only"
                    wrapperClass="flex"
                >
                    @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </x-rapidez::select>
                <x-rapidez::button class="flex items-center" v-on:click="add" dusk="add-to-cart">
                    <x-heroicon-o-shopping-cart class="mr-2 h-5 w-5" v-if="!adding && !added" />
                    <x-heroicon-o-refresh class="mr-2 h-5 w-5 animate-spin" v-if="adding" />
                    <x-heroicon-o-check class="mr-2 h-5 w-5" v-if="added" />
                    <span v-if="!adding && !added">@lang('Add to cart')</span>
                    <span v-if="adding">@lang('Adding')...</span>
                    <span v-if="added">@lang('Added')</span>
                </x-rapidez::button>
            </div>
        @endif
    </div>
</add-to-cart>
