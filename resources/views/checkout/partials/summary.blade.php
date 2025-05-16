<x-rapidez::summary>
    <x-rapidez::summary.row>
        <dt class="flex items-center w-full py-1" v-for="item in cart.items">
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
            <div class="w-7/12">
                <div class="font-semibold">@{{ item.product.name }}</div>
                <div class="last:*:pr-0 *:pr-2 flex flex-wrap gap-x-2 text-xs text-muted">
                    <div class="*:border-r last:*:border-r-0 *:px-2 *:mb-1.5 *:leading-3 -mx-2 flex flex-wrap text-xs -mb-1.5">
                        <div v-for="option in item.configurable_options">
                            @{{ option.option_label }}: @{{ option.value_label }}
                        </div>
                        <div v-for="option in item.customizable_options">
                            @{{ option.label }}: @{{ option.values[0].label || option.values[0].value }}
                        </div>
                        <div v-for="option in config.cart_attributes">
                            <template v-if="item.product.attribute_values?.[option] && typeof item.product.attribute_values[option] === 'object'">
                                @{{ window.attributeLabel(option) }}: <span v-html="item.product.attribute_values[option]?.join(', ')"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-2/12 text-right">@{{ item.quantity }}</div>
            <div class="w-3/12 text-right">@{{ item.prices.row_total.value | price }}</div>
        </dt>
    </x-rapidez::summary.row>

    <x-rapidez::summary.divider />

    <x-rapidez::summary.row>
        <x-rapidez::summary.title>@lang('Subtotal')</x-rapidez::summary.title>
        <x-rapidez::summary.value>
            @{{ cart.prices.subtotal_including_tax.value | price }}
        </x-rapidez::summary.value>
    </x-rapidez::summary.row>

    <template v-if="cart.shipping_addresses.length && cart.shipping_addresses[0]?.selected_shipping_method">
        <x-rapidez::summary.divider />
        <x-rapidez::summary.row>
            <x-rapidez::summary.title>@lang('Shipping')</x-rapidez::summary.title>
            <x-rapidez::summary.value>@{{ cart.shipping_addresses[0]?.selected_shipping_method.amount.value | price }}</x-rapidez::summary.value>
            <x-rapidez::summary.description>@{{ cart.shipping_addresses[0]?.selected_shipping_method.carrier_title }} - @{{ cart.shipping_addresses[0]?.selected_shipping_method.method_title }}</x-rapidez::summary.description>
        </x-rapidez::summary.row>
    </template>

    <x-rapidez::summary.divider />

    <template v-for="discount in cart.prices.discounts">
        <x-rapidez::summary.row>
            <x-rapidez::summary.title>@{{ discount.label }}</x-rapidez::summary.title>
            <x-rapidez::summary.value class="text-green-700 font-semibold">-@{{ discount.amount.value | price }}</x-rapidez::summary.value>
        </x-rapidez::summary.row>
    </template>

    <x-rapidez::summary.row>
        <x-rapidez::summary.title class="font-bold text-base">@lang('Total')</x-rapidez::summary.title>
        <x-rapidez::summary.value class="font-bold text-base">@{{ cart.prices.grand_total.value | price }}</x-rapidez::summary.value>
        <x-rapidez::summary.description v-if="cart.prices.applied_taxes?.[0]?.amount?.value" class="text-right pr-0">
            @lang('Inclusive') @{{ (cart.prices.applied_taxes?.[0]?.amount?.value || 0) | price }} @lang('Tax')
        </x-rapidez::summary.description>
    </x-rapidez::summary.row>

    <x-rapidez::summary.divider />

    <x-rapidez::summary.row>
        <template v-if="cart.shipping_addresses[0]">
            <x-rapidez::summary.title>
                <template v-if="cart.billing_address?.same_as_shipping">@lang('Shipping & billing address')</template>
                <template v-else>@lang('Shipping address')</template>
            </x-rapidez::summary.title>
            <x-rapidez::summary.description>
                <ul>
                    <li v-if="cart.shipping_addresses[0]?.company">@{{ cart.shipping_addresses[0]?.company }}</li>
                    <li v-if="cart.shipping_addresses[0]?.prefix || cart.shipping_addresses[0]?.firstname || cart.shipping_addresses[0]?.middlename || cart.shipping_addresses[0]?.lastname || cart.shipping_addresses[0]?.suffix">@{{ cart.shipping_addresses[0]?.prefix }} @{{ cart.shipping_addresses[0]?.firstname }} @{{ cart.shipping_addresses[0]?.middlename }} @{{ cart.shipping_addresses[0]?.lastname }} @{{ cart.shipping_addresses[0]?.suffix }}</li>
                    <li v-if="cart.shipping_addresses[0]?.street[0] || cart.shipping_addresses[0]?.street[1] || cart.shipping_addresses[0]?.street[2]">@{{ cart.shipping_addresses[0]?.street[0] }} @{{ cart.shipping_addresses[0]?.street[1] }} @{{ cart.shipping_addresses[0]?.street[2] }}</li>
                    <li v-if="cart.shipping_addresses[0]?.postcode || cart.shipping_addresses[0]?.city || cart.shipping_addresses[0]?.country.label">@{{ cart.shipping_addresses[0]?.postcode }} - @{{ cart.shipping_addresses[0]?.city }} - @{{ cart.shipping_addresses[0]?.country.label }}</li>
                    <li v-if="cart.shipping_addresses[0]?.telephone">@{{ cart.shipping_addresses[0]?.telephone }}</li>
                </ul>
            </x-rapidez::summary.description>
        </template>
        <template v-if="cart.billing_address && !cart.billing_address?.same_as_shipping">
            <x-rapidez::summary.divider />
            <x-rapidez::summary.title>
                @lang('Billing address')
            </x-rapidez::summary.title>
            <x-rapidez::summary.description>
                <ul>
                    <li v-if="cart.billing_address.company">@{{ cart.billing_address.company }}</li>
                    <li>@{{ cart.billing_address.prefix }} @{{ cart.billing_address.firstname }} @{{ cart.billing_address.middlename }} @{{ cart.billing_address.lastname }} @{{ cart.billing_address.suffix }}</li>
                    <li>@{{ cart.billing_address.street[0] }} @{{ cart.billing_address.street[1] }} @{{ cart.billing_address.street[2] }}</li>
                    <li>@{{ cart.billing_address.postcode }} - @{{ cart.billing_address.city }} - @{{ cart.billing_address.country.label }}</li>
                    <li v-if="cart.billing_address.telephone">@{{ cart.billing_address.telephone }}</li>
                </ul>
            </x-rapidez::summary.description>
        </template>
    </x-rapidez::summary.row>
</x-rapidez::summary>