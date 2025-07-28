<dl class="mb-5 flex w-full flex-col rounded-lg border *:flex *:flex-wrap *:justify-between *:p-3 *:border-b last:*:border-none">
    <div>
        <dt>@lang('Subtotal')</dt>
        <dd v-if="showTax">@{{ window.price(cart.prices.subtotal_including_tax.value) }}</dd>
        <dd v-else>@{{ window.price(cart.prices.subtotal_excluding_tax.value) }}</dd>
    </div>

    <template v-if="cart.shipping_addresses?.length" v-for="address in cart.shipping_addresses">
        <div v-if="address.selected_shipping_method">
            <dt>@lang('Shipping')</dt>
            <dd v-if="showTax">@{{ window.price(address.selected_shipping_method.price_incl_tax.value) }}</dd>
            <dd v-else>@{{ window.price(address.selected_shipping_method.price_excl_tax.value) }}</dd>
            <small>@{{ address.selected_shipping_method.carrier_title }} - @{{ address.selected_shipping_method.method_title }}</small>
        </div>
    </template>

    <template v-if="cart.prices?.applied_taxes?.length">
        <div v-for="tax in cart.prices.applied_taxes">
            <dt>@{{ tax.label }}</dt>
            <dd>@{{ window.price(tax.amount.value) }}</dd>
        </div>
    </template>

    <template v-if="cart.fixedProductTaxes?.value">
        <div v-for="value, label in cart.fixed_product_taxes">
            <dt>@{{ label }}</dt>
            <dd>@{{ window.price(value) }}</dd>
        </div>
    </template>

    <template v-if="cart.prices?.discounts?.length">
        <div v-for="discount in cart.fixed_product_taxes">
            <dt>@{{ discount.label }}</dt>
            <dd>-@{{ window.price(discount.amount.value) }}</dd>
        </div>
    </template>

    <div>
        <dt>@lang('Total')</dt>
        <dd v-if="showTax">@{{ window.price(cart.prices.grand_total.value) }}</dd>
        <dd v-else>@{{ window.price(cart.prices.grand_total.value - cart.taxTotal.value) }}</dd>
    </div>
</dl>

<div class="w-full" :class="{ 'cursor-not-allowed': !canOrder }">
    <x-rapidez::button.conversion
        href="{{ route('checkout') }}"
        class="w-full text-center"
        v-bind:class="{ 'pointer-events-none': !canOrder }"
        dusk="checkout"
    >
        @lang('Checkout')
    </x-rapidez::button.conversion>
</div>
