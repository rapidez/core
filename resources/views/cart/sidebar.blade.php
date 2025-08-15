<x-rapidez::summary>
    <div>
        <dt>@lang('Subtotal')</dt>
        <dd v-if="showTax">@{{ cart.prices.subtotal_including_tax.value | price }}</dd>
        <dd v-else>@{{ cart.prices.subtotal_excluding_tax.value | price }}</dd>
    </div>

    <template v-if="cart.shipping_addresses?.length">
        <div v-for="address in cart.shipping_addresses" v-if="address.selected_shipping_method">
            <dt>
                @lang('Shipping')
                <small class="text-muted">@{{ address.selected_shipping_method.carrier_title }} - @{{ address.selected_shipping_method.method_title }}</small>
            </dt>
            <dd v-if="showTax">@{{ address.selected_shipping_method.price_incl_tax.value | price }}</dd>
            <dd v-else>@{{ address.selected_shipping_method.price_excl_tax.value | price }}</dd>
        </div>
    </template>

    <template v-if="cart.prices?.applied_taxes?.length">
        <div v-for="tax in cart.prices.applied_taxes">
            <dt>@{{ tax.label }}</dt>
            <dd>@{{ tax.amount.value | price }}</dd>
        </div>
    </template>

    <template v-if="cart.fixedProductTaxes">
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

    <div class="border-t pt-3 mt-3 font-bold">
        <dt>@lang('Total')</dt>
        <dd v-if="showTax">@{{ cart.prices.grand_total.value | price }}</dd>
        <dd v-else>@{{ cart.prices.grand_total.value - cart.taxTotal | price }}</dd>
    </div>
</x-rapidez::summary>

<div class="mt-5 w-full" :class="{ 'cursor-not-allowed': !canOrder }">
    <x-rapidez::button.conversion
        href="{{ route('checkout') }}"
        class="w-full text-center"
        v-bind:class="{ 'pointer-events-none': !canOrder }"
    >
        @lang('Checkout')
    </x-rapidez::button.conversion>
</div>
