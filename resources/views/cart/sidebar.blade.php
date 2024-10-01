<dl class="mb-5 flex w-full flex-col rounded-lg border [&>*]:flex [&>*]:flex-wrap [&>*]:justify-between [&>*]:p-3 [&>*]:border-b [&>*:last-child]:border-none">
    <div>
        <dt>@lang('Subtotal')</dt>
        <dd v-if="showTax">@{{ cart.prices.subtotal_including_tax.value | price }}</dd>
        <dd v-else>@{{ cart.prices.subtotal_excluding_tax.value | price }}</dd>
    </div>

    <template v-if="cart.shipping_addresses?.length">
        <div v-for="address in cart.shipping_addresses">
            <dt>@lang('Shipping')</dt>
            <dd v-if="showTax">@{{ address.selected_shipping_method.price_incl_tax.value | price }}</dd>
            <dd v-else>@{{ address.selected_shipping_method.price_excl_tax.value | price }}</dd>
            <small>@{{ address.selected_shipping_method.carrier_title }} - @{{ address.selected_shipping_method.method_title }}</small>
        </div>
    </template>

    <template v-if="cart.prices?.applied_taxes?.length">
        <div v-for="tax in cart.prices.applied_taxes">
            <dt>@{{ tax.label }}</dt>
            <dd>@{{ tax.amount.value | price }}</dd>
        </div>
    </template>

    <template v-if="cart.fixedProductTaxes?.value">
        <div v-for="value, label in cart.fixed_product_taxes">
            <dt>@{{ label }}</dt>
            <dd>@{{ value | price }}</dd>
        </div>
    </template>

    <template v-if="cart.prices?.discounts?.length">
        <div v-for="discount in cart.fixed_product_taxes">
            <dt>@{{ discount.label }}</dt>
            <dd>-@{{ discount.amount.value | price }}</dd>
        </div>
    </template>

    <div>
        <dt>@lang('Total')</dt>
        <dd v-if="showTax">@{{ cart.prices.grand_total.value | price }}</dd>
        <dd v-else>@{{ cart.prices.grand_total.value - cart.taxTotal.value | price }}</dd>
    </div>
</dl>

<x-rapidez::button href="{{ route('checkout') }}" dusk="checkout" class="w-full text-center">
    @lang('Checkout')
</x-rapidez::button>
