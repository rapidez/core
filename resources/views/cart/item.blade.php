<table class="w-full border-b">
    <tbody class="divide-y">
        <tr v-for="(item, index) in cart.items" class="flex-wrap max-md:flex *:p-2 md:*:p-4">
            <td class="w-24">
                <a :href="item.product.url_key + item.product.url_suffix | url">
                    <img
                        v-if="item.configured_variant?.image"
                        class="mx-auto"
                        :alt="item.product.name"
                        :src="resizedPath(item.configured_variant.image.url + '.webp', '80x80')"
                        height="80"
                    />
                    <img
                        v-else-if="item.product.image"
                        class="mx-auto"
                        :alt="item.product.name"
                        :src="resizedPath(item.product.image.url + '.webp', '80x80')"
                        height="80"
                    />
                </a>
            </td>
            <td class="items-center max-md:flex flex-1">
                <div class="flex flex-col items-start">
                    <a :href="item.product.url_key + item.product.url_suffix | url" class="font-bold" dusk="cart-item-name">
                        @{{ item.product.name }}
                        <div class="text-red-600" v-if="!item.is_available">
                            @lang('This product is out of stock, remove it to continue your order.')
                        </div>
                    </a>
                    <div v-for="option in item.configurable_options">
                        @{{ option.option_label }}: @{{ option.value_label }}
                    </div>
                    <div v-for="option in item.customizable_options">
                        @{{ option.label }}: @{{ option.values[0].label || option.values[0].value }}
                    </div>
                    <div v-for="option in config.cart_attributes">
                        <template v-if="item.product.attribute_values?.[option] && typeof item.product.attribute_values[option] === 'object'">
                            @{{ $root.attributeLabel(option) }}: <span v-html="item.product.attribute_values[option]?.join(', ')"></span>
                        </template>
                    </div>
                    @include('rapidez::cart.item.remove')
                    <div v-if="item.qty_backordered" class="flex gap-2">
                        <x-heroicon-o-exclamation-circle class="mt-px w-5" />
                        <span>
                            <template v-if="item.qty_backordered < item.qty">
                                @lang(':count of the requested quantity will be backordered', ['count' => '@{{ item.qty_backordered }}'])
                            </template>
                            <template v-else>
                                @lang('This product will be backordered')
                            </template>
                        </span>
                    </div>

                </div>
            </td>
            <td class="justify-center text-right max-md:flex max-md:w-full">
                <div class="inline-flex flex-1 items-center justify-between gap-2 md:gap-5">
                    <span class="w-20 text-left">@{{ item.prices.price_including_tax.value | price }}</span>
                    @include('rapidez::cart.item.quantity')
                    <span class="w-20">@{{ item.prices.row_total_including_tax.value | price }}</span>
                </div>
            </td>
        </tr>
    </tbody>
</table>
