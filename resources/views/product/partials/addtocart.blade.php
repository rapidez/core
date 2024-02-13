<add-to-cart :default-qty="{{ $product->min_sale_qty > $product->qty_increments ? $product->min_sale_qty : $product->qty_increments }}">
    <form slot-scope="{ _renderProxy: addToCartSlotProps, options, customOptions, error, add, disabledOptions, simpleProduct, adding, added, price, specialPrice, setCustomOptionFile }" v-on:submit.prevent="add">
        <h1 class="mb-3 text-3xl font-bold" itemprop="name">{{ $product->name }}</h1>
        @if (!$product->in_stock)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            <div v-cloak v-for="(superAttribute, superAttributeId) in config.product.super_attributes">
                <x-rapidez::input-field.select
                    required
                    class="mb-3 w-64 gap-y-0"
                    v-model="options[superAttributeId]"
                >
                    <x-slot:label>@{{ superAttribute.label }}</x-slot:label>
                    <x-slot:input
                        v-bind:id="'super_attribute_'+superAttributeId"
                        v-bind:name="superAttributeId"
                        v-bind:dusk="'super_attribute_'+superAttributeId"
                    >
                        <option disabled selected hidden :value="undefined">@lang('Select') @{{ superAttribute.label.toLowerCase() }}</option>
                        <option
                            v-for="option in Object.values(config.product['super_'+superAttribute.code]).sort((a, b) => a.sort_order - b.sort_order)"
                            v-text="option.label"
                            :value="option.value"
                            :disabled="disabledOptions['super_'+superAttribute.code].includes(option.value)"
                        >
                    </x-slot:input>
                </x-rapidez::input-field.select>
            </div>

            @include('rapidez::product.partials.options')
            <div class="mt-5 flex flex-wrap items-center gap-3" v-blur>
                <div>
                    <div class="text-2xl font-bold text-neutral" v-text="$options.filters.price(specialPrice || price)">
                        {{ price($product->special_price ?: $product->price) }}
                    </div>
                    <div class="text-lg text-neutral line-through" v-if="specialPrice" v-text="$options.filters.price(price)">
                        {{ $product->special_price ? price($product->price) : '' }}
                    </div>
                </div>
                <x-rapidez::input-field.select
                    class="w-auto"
                    name="qty"
                    dusk="qty"
                    label="Quantity" sr-only-label
                    v-model="addToCartSlotProps.qty"
                >
                    <x-slot:input>
                        @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </x-slot:input>
                </x-rapidez::input-field.select>
                <x-rapidez::button type="submit" class="flex items-center" dusk="add-to-cart">
                    <x-heroicon-o-shopping-cart class="mr-2 h-5 w-5" v-if="!adding && !added" />
                    <x-heroicon-o-arrow-path class="mr-2 h-5 w-5 animate-spin" v-if="adding" />
                    <x-heroicon-o-check class="mr-2 h-5 w-5" v-if="added" />
                    <span v-if="!adding && !added">@lang('Add to cart')</span>
                    <span v-if="adding" v-cloak>@lang('Adding')...</span>
                    <span v-if="added" v-cloak>@lang('Added')</span>
                </x-rapidez::button>
            </div>
        @endif
    </form>
</add-to-cart>
