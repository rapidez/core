<div class="shrink-0 max-lg:mt-6 max-lg:border-t max-lg:border-dashed max-lg:pt-6 lg:w-80">
    <strong class="font-heading text-primary-emphasis mb-4 block text-xl font-bold">@lang('Order summary')</strong>
    <div class="mb-4 flex flex-col gap-1">
        <div v-for="item in cart.items" class="flex justify-between text-xs">
            <span class="truncate">@{{ item.quantity }}x @{{ item.product.name }}</span>
            <span>@{{ item.prices.row_total.value | price }}</span>
        </div>
    </div>
    <dl class="flex w-full flex-col gap-4 text-sm first:*:pt-0 last:*:pb-0">
        <div class="flex flex-wrap items-baseline justify-between font-medium">
            <dt class="flex-1">@lang('Subtotal')</dt>
            <dd class="text-right">@{{ cart.prices.subtotal_including_tax.value | price }}</dd>
        </div>
        <div class="w-full border-t border-dashed"></div>
        <div v-if="cart.prices.applied_taxes.length" class="flex flex-wrap items-baseline justify-between font-medium">
            <dt class="flex-1">@lang('Tax')</dt>
            <dd class="text-right">@{{ cart.prices.applied_taxes[0].amount.value | price }}</dd>
        </div>
        <div v-if="cart.shipping_addresses.length && cart.shipping_addresses[0]?.selected_shipping_method" class="flex flex-wrap items-baseline justify-between font-medium">
            <dt class="flex-1">
                @lang('Shipping')
                <div class="text-xs text-muted mt-1">
                    @{{ cart.shipping_addresses[0]?.selected_shipping_method.carrier_title }} - @{{ cart.shipping_addresses[0]?.selected_shipping_method.method_title }}
                </div>
            </dt>
            <dd class="text-right">
                <span v-if="cart.shipping_addresses[0]?.selected_shipping_method.amount.value === 0" class="font-semibold text-green-700">@lang('Free')</span>
                <span v-else>@{{ cart.shipping_addresses[0]?.selected_shipping_method.amount.value | price }}</span>
            </dd>
        </div>
        <div v-for="discount in cart.prices.discounts" class="flex flex-wrap items-baseline justify-between font-medium">
            <dt class="flex-1">@{{ discount.label }}</dt>
            <dd class="text-right">-@{{ discount.amount.value | price }}</dd>
        </div>
        <div class="w-full border-t"></div>
        <div class="flex flex-wrap items-baseline justify-between font-bold">
            <dt class="flex-1">@lang('Total')</dt>
            <dd class="text-right text-base">@{{ cart.prices.grand_total.value | price }}</dd>
        </div>
        <dd v-if="cart.prices.applied_taxes.length" class="flex w-full flex-col gap-1 pr-0 text-right text-xs text-muted">
            @lang('Including') @{{ cart.prices.applied_taxes[0].amount.value | price }} @lang('tax')
        </dd>
        <div v-if="cart.shipping_addresses[0]" class="w-full border-t border-dashed"></div>
        <div v-if="cart.shipping_addresses[0]" class="text-xs text-muted">
            <div class="flex flex-col gap-1 pr-24">
                <span v-if="cart.shipping_addresses[0]?.company">@{{ cart.shipping_addresses[0]?.company }}</span>
                <span>@{{ cart.shipping_addresses[0]?.prefix }} @{{ cart.shipping_addresses[0]?.firstname }} @{{ cart.shipping_addresses[0]?.middlename }} @{{ cart.shipping_addresses[0]?.lastname }} @{{ cart.shipping_addresses[0]?.suffix }}</span>
                <span>@{{ cart.shipping_addresses[0]?.street[0] }} @{{ cart.shipping_addresses[0]?.street[1] }} @{{ cart.shipping_addresses[0]?.street[2] }}</span>
                <span>@{{ cart.shipping_addresses[0]?.postcode }} @{{ cart.shipping_addresses[0]?.city }} @{{ cart.shipping_addresses[0]?.country.label }}</span>
                <span v-if="cart.shipping_addresses[0]?.telephone">@{{ cart.shipping_addresses[0]?.telephone }}</span>
            </div>
        </div>
        <div v-if="cart.billing_address && !cart.billing_address?.same_as_shipping" class="w-full border-t border-dashed"></div>
        <div v-if="cart.billing_address && !cart.billing_address?.same_as_shipping" class="text-xs text-muted">
            <div class="flex flex-col gap-1">
                <span v-if="cart.billing_address.company">@{{ cart.billing_address.company }}</span>
                <span>@{{ cart.billing_address.prefix }} @{{ cart.billing_address.firstname }} @{{ cart.billing_address.middlename }} @{{ cart.billing_address.lastname }} @{{ cart.billing_address.suffix }}</span>
                <span>@{{ cart.billing_address.street[0] }} @{{ cart.billing_address.street[1] }} @{{ cart.billing_address.street[2] }}</span>
                <span>@{{ cart.billing_address.postcode }} @{{ cart.billing_address.city }} @{{ cart.billing_address.country.label }}</span>
                <span v-if="cart.billing_address.telephone">@{{ cart.billing_address.telephone }}</span>
            </div>
        </div>
    </dl>
</div>
