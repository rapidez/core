<table class="w-full border-b">
    <tbody class="divide-y">
        <tr v-for="(item, index) in cart.items" class="flex-wrap max-md:flex [&>*]:p-2 md:[&>*]:p-4">
            <td class="w-24">
                <a :href="item.product.url_key + item.product.url_suffix | url">
                    <img
                        class="mx-auto"
                        :alt="item.product.name"
                        :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento' + item.product.image.url.replace(config.media_url, '') + '.webp'"
                        height="80"
                    />
                </a>
            </td>
            <td class="items-center max-md:flex flex-1">
                <div class="flex flex-col items-start">
                    <a :href="item.product.url_key + item.product.url_suffix | url" class="font-bold" dusk="cart-item-name">
                        @{{ item.product.name }}
                    </a>
                    <div v-for="option in item.configurable_options">
                        @{{ option.option_label }}: @{{ option.value_label }}
                    </div>
                    <div v-for="option in item.customizable_options">
                        @{{ option.label }}: @{{ option.values[0].label || option.values[0].value }}
                    </div>
                    <div v-for="option in config.cart_attributes">
                        <template v-if="item.product.attribute_values?.[option] && typeof item.product.attribute_values[option] === 'object'">
                            @{{ option }}: <span v-html="item.product.attribute_values[option]?.join(', ')"></span>
                        </template>
                    </div>
                    @include('rapidez::cart.item.remove')
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
