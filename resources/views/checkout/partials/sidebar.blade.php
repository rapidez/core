<div v-if="cart?.entity_id" class="p-3 border rounded">
    <div class="flex flex-col w-full">
        <div class="flex gap-x-1 py-3 border-b" v-for="item in cart.items">
            <div class="w-7/12">@{{ item.name }}</div>
            <div class="w-2/12 text-right px-4">@{{ item.qty }}</div>
            <div class="w-3/12 text-right">@{{ item.price | price }}</div>
        </div>
        <div class="flex gap-x-1 py-3 border-b last:font-bold last:border-b-0" v-for="total_segment in checkout.totals.total_segments" v-if="total_segment.value">
            <div class="w-7/12">@{{ total_segment.title }}</div>
            <div class="w-2/12 px-4"></div>
            <div class="w-3/12 text-right">@{{ total_segment.value | price }}</div>
        </div>
    </div>
</div>

<div v-if="$root.checkout.shipping_address" class="w-full flex flex-col gap-x-1 p-3 border mt-4">
    <p class="text-neutral font-lg font-bold mb-2">@lang('Shipping address')</p>
    <ul>
        <li>@{{ $root.checkout?.shipping_address?.firstname }} @{{ $root.checkout?.shipping_address?.lastname }}</li>
        <li>@{{ $root.checkout?.shipping_address?.street[0] }} @{{ $root.checkout?.shipping_address?.street[1] }} @{{ $root.checkout?.billing_address?.street[2] }}</li>
        <li>@{{ $root.checkout?.shipping_address?.postcode }} - @{{ $root.checkout?.shipping_address?.city }} - @{{ $root.checkout?.shipping_address?.country_id }}</li>
        <li>@{{ $root.checkout?.shipping_address?.telephone }}</li>
        <li>@{{ $root.checkout?.shipping_address?.company }}</li>
    </ul>
</div>
