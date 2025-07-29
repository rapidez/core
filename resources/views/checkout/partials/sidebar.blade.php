<div class="flex flex-col gap-5">
    <div class="rounded border p-3">
        <div class="flex w-full flex-col">
            <div v-for="item in cart.value.items" class="flex gap-x-1 border-b last:border-b-0 py-3">
                <div class="w-7/12">@{{ item.product.name }}</div>
                <div class="w-2/12 px-4 text-right">@{{ item.quantity }}</div>
                <div class="w-3/12 text-right">@{{ window.price(item.prices.row_total.value) }}</div>
            </div>
        </div>
    </div>

    <dl class="flex w-full flex-col rounded border *:flex *:flex-wrap *:justify-between *:p-3 *:border-b last:*:border-none">
        <div>
            <dt>@lang('Subtotal')</dt>
            <dd>@{{ window.price(cart.value.prices.subtotal_including_tax.value) }}</dd>
        </div>
        <div v-if="cart.value.prices.applied_taxes.length">
            <dt>@lang('Tax')</dt>
            <dd>@{{ window.price(cart.value.prices.applied_taxes[0].amount.value) }}</dd>
        </div>
        <div v-if="cart.value.shipping_addresses.length &&cart.value.shipping_addresses[0]?.selected_shipping_method">
            <dt>
                @lang('Shipping')<br>
                <small>@{{ cart.value.shipping_addresses[0]?.selected_shipping_method.carrier_title }} - @{{ cart.value.shipping_addresses[0]?.selected_shipping_method.method_title }}</small>
            </dt>
            <dd>@{{ window.price(cart.value.shipping_addresses[0]?.selected_shipping_method.amount.value) }}</dd>
        </div>
        <div v-for="discount in cart.value.prices.discounts">
            <dt>@{{ discount.label }}</dt>
            <dd>-@{{ window.price(discount.amount.value) }}</dd>
        </div>
        <div class="font-bold">
            <dt>@lang('Total')</dt>
            <dd>@{{ window.price(cart.value.prices.grand_total.value) }}</dd>
        </div>
    </dl>

    <div v-if="cart.value.shipping_addresses[0]" class="flex w-full flex-col gap-x-1 border p-3 rounded">
        <p class="font-lg mb-2 font-bold">
            <template v-if="cart.value.billing_address?.same_as_shipping">@lang('Shipping & billing address')</template>
            <template v-else>@lang('Shipping address')</template>
        </p>
        <ul>
            <li v-if="cart.value.shipping_addresses[0]?.company">@{{ cart.value.shipping_addresses[0]?.company }}</li>
            <li>@{{ cart.value.shipping_addresses[0]?.prefix }} @{{ cart.value.shipping_addresses[0]?.firstname }} @{{ cart.value.shipping_addresses[0]?.middlename }} @{{ cart.value.shipping_addresses[0]?.lastname }} @{{ cart.value.shipping_addresses[0]?.suffix }}</li>
            <li>@{{ cart.value.shipping_addresses[0]?.street[0] }} @{{ cart.value.shipping_addresses[0]?.street[1] }} @{{ cart.value.shipping_addresses[0]?.street[2] }}</li>
            <li>@{{ cart.value.shipping_addresses[0]?.postcode }} - @{{ cart.value.shipping_addresses[0]?.city }} - @{{ cart.value.shipping_addresses[0]?.country.label }}</li>
            <li v-if="cart.value.shipping_addresses[0]?.telephone">@{{ cart.value.shipping_addresses[0]?.telephone }}</li>
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
