<div class="flex flex-col">
    <x-rapidez::summary class="gap-y-4">
        <div v-for="item in cart.items">
            <dt>
                <div class="flex">
                    <div class="flex shrink-0 size-10 mr-2">
                        <img
                            v-if="item.configured_variant?.image"
                            class="w-10 h-auto object-contain"
                            :alt="item.product.name"
                            :src="resizedPath(item.configured_variant.image.url + '.webp', '80x80')"
                            loading="lazy"
                            width="80"
                            height="80"
                        />
                        <img
                            v-else-if="item.product.image"
                            class="w-10 h-auto object-contain"
                            :alt="item.product.name"
                            :src="resizedPath(item.product.image.url + '.webp', '80x80')"
                            loading="lazy"
                            width="80"
                            height="80"
                        />
                    </div>
                    <div class="flex-1">
                        <div class="mb-1">@{{ item.product.name }}</div>
                        <div class="last:*:pr-0 *:pr-2 flex flex-wrap gap-x-2 text-xs text-muted">
                            <div class="*:border-r last:*:border-r-0 *:px-2 *:mb-1.5 *:leading-3 -mx-2 flex flex-wrap text-xs -mb-1.5">
                                <div>
                                    @{{ item.quantity }}x
                                </div>
                                <div v-for="option in item.configurable_options">
                                    @{{ option.option_label }}: @{{ option.value_label }}
                                </div>
                                <div v-for="option in item.customizable_options">
                                    @{{ option.label }}: @{{ option.values[0].label || option.values[0].value }}
                                </div>
                                <div v-for="option in config.cart_attributes">
                                    <template v-if="item.product.attribute_values?.[option] && typeof item.product.attribute_values[option].value === 'object'">
                                        @{{ item.product.attribute_values[option].label }}:
                                        <span v-html="item.product.attribute_values[option].value.join(', ')"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </dt>
            <dd class="text-right">@{{ window.price(item.prices.row_total.value) }}</dd>
        </div>
    </x-rapidez::summary>

    <x-rapidez::summary class="border-t pt-4 mt-5 mb-5">
        <div>
            <dt>@lang('Subtotal')</dt>
            <dd>@{{ window.price(cart.value.prices.subtotal_including_tax.value) }}</dd>
        </div>
        <div v-if="cart.value.prices.applied_taxes.length">
            <dt>@lang('Tax')</dt>
            <dd>@{{ window.price(cart.value.prices.applied_taxes[0].amount.value) }}</dd>
        </div>
        <div v-if="cart.value.shipping_addresses.length && cart.value.shipping_addresses?.[0]?.selected_shipping_method">
            <dt>
                @lang('Shipping')<br>
                <small class="text-muted">@{{ cart.value.shipping_addresses[0].selected_shipping_method.carrier_title }} - @{{ cart.value.shipping_addresses[0].selected_shipping_method.method_title }}</small>
            </dt>
            <dd>@{{ window.price(cart.value.shipping_addresses[0].selected_shipping_method.amount.value) }}</dd>
        </div>
        <div v-for="discount in cart.value.prices.discounts" class="border-t pt-3 mt-3">
            <dt>@{{ discount.label }}</dt>
            <dd class="text-green-700 font-medium">-@{{ window.price(discount.amount.value) }}</dd>
        </div>
        <div class="font-bold">
            <dt>@lang('Total')</dt>
            <dd>@{{ window.price(cart.value.prices.grand_total.value) }}</dd>
        </div>
    </x-rapidez::summary>

    <div v-if="cart.value.shipping_addresses?.[0]" class="flex w-full flex-col gap-x-1 bg px-5 py-4 rounded">
        <p class="font-lg mb-2 font-bold">
            <template v-if="cart.value.billing_address?.same_as_shipping">@lang('Shipping & billing address')</template>
            <template v-else>@lang('Shipping address')</template>
        </p>
        <ul>
            <li v-if="cart.value.shipping_addresses[0].company">@{{ cart.value.shipping_addresses[0].company }}</li>
            <li>@{{ cart.value.shipping_addresses[0].prefix }} @{{ cart.value.shipping_addresses[0].firstname }} @{{ cart.value.shipping_addresses[0].middlename }} @{{ cart.value.shipping_addresses[0].lastname }} @{{ cart.value.shipping_addresses[0].suffix }}</li>
            <li>@{{ cart.value.shipping_addresses[0].street[0] }} @{{ cart.value.shipping_addresses[0].street[1] }} @{{ cart.value.shipping_addresses[0].street[2] }}</li>
            <li>@{{ cart.value.shipping_addresses[0].postcode }} - @{{ cart.value.shipping_addresses[0].city }} - @{{ cart.value.shipping_addresses[0].country.label }}</li>
            <li v-if="cart.value.shipping_addresses[0].telephone">@{{ cart.value.shipping_addresses[0].telephone }}</li>
        </ul>
    </div>
    <div v-if="cart.value.billing_address && !cart.value.billing_address?.same_as_shipping" class="mt-4 flex w-full flex-col gap-x-1 border p-3">
        <p class="font-lg mb-2 font-bold">@lang('Billing address')</p>
        <ul>
            <li v-if="cart.value.billing_address.company">@{{ cart.value.billing_address.company }}</li>
            <li>@{{ cart.value.billing_address.prefix }} @{{ cart.value.billing_address.firstname }} @{{ cart.value.billing_address.middlename }} @{{ cart.value.billing_address.lastname }} @{{ cart.value.billing_address.suffix }}</li>
            <li>@{{ cart.value.billing_address.street[0] }} @{{ cart.value.billing_address.street[1] }} @{{ cart.value.billing_address.street[2] }}</li>
            <li>@{{ cart.value.billing_address.postcode }} - @{{ cart.value.billing_address.city }} - @{{ cart.value.billing_address.country.label }}</li>
            <li v-if="cart.value.billing_address.telephone">@{{ cart.value.billing_address.telephone }}</li>
        </ul>
    </div>
</div>
