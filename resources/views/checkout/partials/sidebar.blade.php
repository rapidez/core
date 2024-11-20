<div class="flex flex-col gap-5">
    <div class="rounded border p-3">
        <div class="flex w-full flex-col">
            <div v-for="item in cart.items" class="flex gap-x-1 border-b last:border-b-0 py-3">
                <div class="w-7/12">@{{ item.product.name }}</div>
                <div class="w-2/12 px-4 text-right">@{{ item.quantity }}</div>
                <div class="w-3/12 text-right">@{{ item.prices.row_total.value | price }}</div>
            </div>
        </div>
    </div>

    <dl class="flex w-full flex-col rounded border *:flex *:flex-wrap *:justify-between *:p-3 *:border-b last:*:border-none">
        <div>
            <dt>@lang('Subtotal')</dt>
            <dd>@{{ cart.prices.subtotal_including_tax.value | price }}</dd>
        </div>
        <div v-if="cart.prices.applied_taxes.length">
            <dt>@lang('Tax')</dt>
            <dd>@{{ cart.prices.applied_taxes[0].amount.value | price }}</dd>
        </div>
        <div v-if="cart.shipping_addresses.length && cart.shipping_addresses[0]?.selected_shipping_method">
            <dt>
                @lang('Shipping')<br>
                <small>@{{ cart.shipping_addresses[0]?.selected_shipping_method.carrier_title }} - @{{ cart.shipping_addresses[0]?.selected_shipping_method.method_title }}</small>
            </dt>
            <dd>@{{ cart.shipping_addresses[0]?.selected_shipping_method.amount.value | price }}</dd>
        </div>
        <div v-for="discount in cart.prices.discounts">
            <dt>@{{ discount.label }}</dt>
            <dd>-@{{ discount.amount.value | price }}</dd>
        </div>
        <div class="font-bold">
            <dt>@lang('Total')</dt>
            <dd>@{{ cart.prices.grand_total.value | price }}</dd>
        </div>
    </dl>

    <div v-if="cart.shipping_addresses[0]" class="flex w-full flex-col gap-x-1 border p-3 rounded">
        <p class="font-lg mb-2 font-bold text-neutral">
            <template v-if="cart.billing_address?.same_as_shipping">@lang('Shipping & billing address')</template>
            <template v-else>@lang('Shipping address')</template>
        </p>
        <ul>
            <li v-if="cart.shipping_addresses[0]?.company">@{{ cart.shipping_addresses[0]?.company }}</li>
            <li>@{{ cart.shipping_addresses[0]?.prefix }} @{{ cart.shipping_addresses[0]?.firstname }} @{{ cart.shipping_addresses[0]?.middlename }} @{{ cart.shipping_addresses[0]?.lastname }} @{{ cart.shipping_addresses[0]?.suffix }}</li>
            <li>@{{ cart.shipping_addresses[0]?.street[0] }} @{{ cart.shipping_addresses[0]?.street[1] }} @{{ cart.shipping_addresses[0]?.street[2] }}</li>
            <li>@{{ cart.shipping_addresses[0]?.postcode }} - @{{ cart.shipping_addresses[0]?.city }} - @{{ cart.shipping_addresses[0]?.country.label }}</li>
            <li v-if="cart.shipping_addresses[0]?.telephone">@{{ cart.shipping_addresses[0]?.telephone }}</li>
        </ul>
    </div>
    <div v-if="cart.billing_address && !cart.billing_address?.same_as_shipping" class="mt-4 flex w-full flex-col gap-x-1 border p-3">
        <p class="font-lg mb-2 font-bold text-neutral">@lang('Billing address')</p>
        <ul>
            <li v-if="cart.billing_address.company">@{{ cart.billing_address.company }}</li>
            <li>@{{ cart.billing_address.prefix }} @{{ cart.billing_address.firstname }} @{{ cart.billing_address.middlename }} @{{ cart.billing_address.lastname }} @{{ cart.billing_address.suffix }}</li>
            <li>@{{ cart.billing_address.street[0] }} @{{ cart.billing_address.street[1] }} @{{ cart.billing_address.street[2] }}</li>
            <li>@{{ cart.billing_address.postcode }} - @{{ cart.billing_address.city }} - @{{ cart.billing_address.country.label }}</li>
            <li v-if="cart.billing_address.telephone">@{{ cart.billing_address.telephone }}</li>
        </ul>
    </div>
</div>
