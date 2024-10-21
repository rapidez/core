<div v-if="$root.cart?.id" class="rounded border p-3">
    <div class="flex w-full flex-col">
        <div v-for="item in $root.cart.items" class="flex gap-x-1 border-b py-3">
            <div class="w-7/12">@{{ item.product.name }}</div>
            <div class="w-2/12 px-4 text-right">@{{ item.quantity }}</div>
            <div class="w-3/12 text-right">@{{ item.prices.row_total.value | price }}</div>
        </div>
        <div v-for="total_segment in checkoutScope.checkout.totals.total_segments" v-if="total_segment.value" class="flex gap-x-1 border-b py-3 last:border-b-0 last:font-bold">
            <div class="w-7/12">@{{ total_segment.title }}</div>
            <div class="w-2/12 px-4"></div>
            <div class="w-3/12 text-right">@{{ total_segment.value | price }}</div>
        </div>
    </div>
</div>
<div v-if="$root.checkout.shipping_address" class="mt-4 flex w-full flex-col gap-x-1 border p-3">
    <p class="font-lg mb-2 font-bold text-neutral" v-text="$root.checkout.hide_billing ? '{{ __('Shipping & billing address') }}' : '{{ __('Shipping address') }}' "></p>
    <ul>
        <li>@{{ $root.checkout?.shipping_address?.firstname }} @{{ $root.checkout?.shipping_address?.lastname }}</li>
        <li>@{{ $root.checkout?.shipping_address?.street[0] }} @{{ $root.checkout?.shipping_address?.street[1] }} @{{ $root.checkout?.billing_address?.street[2] }}</li>
        <li>@{{ $root.checkout?.shipping_address?.postcode }} - @{{ $root.checkout?.shipping_address?.city }} - @{{ $root.checkout?.shipping_address?.country_id }}</li>
        <li>@{{ $root.checkout?.shipping_address?.telephone }}</li>
        <li>@{{ $root.checkout?.shipping_address?.company }}</li>
    </ul>
</div>
<div v-if="$root.checkout.billing_address && !$root.checkout.hide_billing" class="mt-4 flex w-full flex-col gap-x-1 border p-3">
    <p class="font-lg mb-2 font-bold text-neutral">@lang('Billing address')</p>
    <ul>
        <li>@{{ $root.checkout?.billing_address?.firstname }} @{{ $root.checkout?.billing_address?.lastname }}</li>
        <li>@{{ $root.checkout?.billing_address?.street[0] }} @{{ $root.checkout?.billing_address?.street[1] }} @{{ $root.checkout?.billing_address?.street[2] }}</li>
        <li>@{{ $root.checkout?.billing_address?.postcode }} - @{{ $root.checkout?.billing_address?.city }} - @{{ $root.checkout?.billing_address?.country_id }}</li>
        <li>@{{ $root.checkout?.billing_address?.telephone }}</li>
        <li>@{{ $root.checkout?.billing_address?.company }}</li>
    </ul>
</div>
