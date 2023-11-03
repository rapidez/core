<dl class="mb-5 flex w-full flex-col rounded-lg border [&>*]:flex [&>*]:flex-wrap [&>*]:justify-between [&>*]:p-3 [&>*]:border-b [&>*:last-child]:border-none">
    <div>
        <dt>@lang('Subtotal')</dt>
        <dd>@{{ data.cart.prices.subtotal_including_tax.value | price }}</dd>
    </div>
    <div v-if="data.cart.prices.applied_taxes.length">
        <dt>@lang('Tax')</dt>
        <dd>@{{ data.cart.prices.applied_taxes[0].amount.value | price }}</dd>
    </div>
    <div v-if="data.cart.shipping_addresses.length">
        <dt>
            @lang('Shipping')<br>
            <small>@{{ data.cart.shipping_addresses[0].selected_shipping_method.carrier_title }} - @{{ data.cart.shipping_addresses[0].selected_shipping_method.method_title }}</small>
        </dt>
        <dd>@{{ data.cart.shipping_addresses[0].selected_shipping_method.amount.value | price }}</dd>
    </div>
    <div v-for="discount in data.cart.prices.discounts">
        <dt>@{{ discount.label }}</dt>
        <dd>-@{{ discount.amount.value | price }}</dd>
    </div>
    <div class="font-bold">
        <dt>@lang('Total')</dt>
        <dd>@{{ data.cart.prices.grand_total.value | price }}</dd>
    </div>
</dl>

<x-rapidez::button href="{{ route('checkout') }}" dusk="checkout" class="w-full text-center">
    @lang('Checkout')
</x-rapidez::button>
