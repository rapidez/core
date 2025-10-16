<x-rapidez::summary>
    <div>
        <dt>@lang('Subtotal')</dt>
        <dd v-if="showTax">@{{ window.price(cart.value.prices.subtotal_including_tax.value) }}</dd>
        <dd v-else>@{{ window.price(cart.value.prices.subtotal_excluding_tax.value) }}</dd>
    </div>

    <template v-if="cart.value.shipping_addresses?.length">
        <div v-for="address in cart.value.shipping_addresses" v-if="address.selected_shipping_method">
            <dt>
                @lang('Shipping')
                <small class="text-muted">@{{ address.selected_shipping_method.carrier_title }} - @{{ address.selected_shipping_method.method_title }}</small>
            </dt>
            <dd v-if="showTax">@{{ window.price(address.selected_shipping_method.price_incl_tax.value) }}</dd>
            <dd v-else>@{{ window.price(address.selected_shipping_method.price_excl_tax.value) }}</dd>
        </div>
    </template>

    <template v-if="cart.value.prices?.applied_taxes?.length">
        <div v-for="tax in cart.value.prices.applied_taxes">
            <dt>@{{ tax.label }}</dt>
            <dd>@{{ window.price(tax.amount.value) }}</dd>
        </div>
    </template>

    <template v-if="cart.value.fixedProductTaxes?.value">
        <div v-for="value, label in cart.value.fixed_product_taxes">
            <dt>@{{ label }}</dt>
            <dd>@{{ window.price(value) }}</dd>
        </div>
    </template>

    <template v-if="cart.value.prices?.discounts?.length">
        <div v-for="discount in cart.value.fixed_product_taxes">
            <dt>@{{ discount.label }}</dt>
            <dd>-@{{ window.price(discount.amount.value) }}</dd>
        </div>
    </template>

    <div class="border-t pt-3 mt-3 font-bold">
        <dt>@lang('Total')</dt>
        <dd v-if="showTax">@{{ window.price(cart.value.prices.grand_total.value) }}</dd>
        <dd v-else>@{{ window.price(cart.value.prices.grand_total.value - cart.value.taxTotal.value) }}</dd>
    </div>
</x-rapidez::summary>

<div class="mt-5 w-full" :class="{ 'cursor-not-allowed': !canOrder }">
    <x-rapidez::button.conversion
        href="{{ route('checkout') }}"
        class="w-full text-center"
        v-bind:class="{ 'pointer-events-none': !canOrder }"
        loader
    >
        @lang('Checkout')
    </x-rapidez::button.conversion>
</div>
