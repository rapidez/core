<x-rapidez::summary>
    <x-rapidez::summary.row>
        <x-rapidez::summary.title>@lang('Subtotal')</x-rapidez::summary.title>
        <x-rapidez::summary.value>
            <template v-if="showTax">@{{ cart.prices.subtotal_including_tax.value | price }}</template>
            <template v-else>@{{ cart.prices.subtotal_excluding_tax.value | price }}</template>
        </x-rapidez::summary.value>
    </x-rapidez::summary.row>

    <template v-if="cart.shipping_addresses?.length">
        <template v-for="address in cart.shipping_addresses" v-if="address.selected_shipping_method">
            <x-rapidez::summary.divider />
            <x-rapidez::summary.row>
                <x-rapidez::summary.title>@lang('Shipping')</x-rapidez::summary.title>
                <x-rapidez::summary.value>
                    <template v-if="showTax">@{{ address.selected_shipping_method.price_incl_tax.value | price }}</template>
                    <template v-else>@{{ address.selected_shipping_method.price_excl_tax.value | price }}</template>
                </x-rapidez::summary.value>
                <x-rapidez::summary.description>@{{ address.selected_shipping_method.carrier_title }} - @{{ address.selected_shipping_method.method_title }}</x-rapidez::summary.description>
            </x-rapidez::summary.row>
        </template>
    </template>

    <template v-if="cart.prices?.applied_taxes?.length">
        <template v-for="tax in cart.prices.applied_taxes">
            <x-rapidez::summary.divider />
            <x-rapidez::summary.row>
                <x-rapidez::summary.title>@{{ tax.label }}</x-rapidez::summary.title>
                <x-rapidez::summary.value>@{{ tax.amount.value | price }}</x-rapidez::summary.value>
            </x-rapidez::summary.row>
        </template>
    </template>

    <template v-if="cart.fixedProductTaxes?.value">
        <template v-for="value, label in cart.fixed_product_taxes">
            <x-rapidez::summary.divider />
            <x-rapidez::summary.row>
                <x-rapidez::summary.title>@{{ label }}</x-rapidez::summary.title>
                <x-rapidez::summary.value>@{{ value | price }}</x-rapidez::summary.value>
            </x-rapidez::summary.row>
        </template>
    </template>

    <template v-if="cart.prices?.discounts?.length">
        <template v-for="discount in cart.prices.discounts">
            <x-rapidez::summary.divider />
            <x-rapidez::summary.row>
                <x-rapidez::summary.title>@{{ discount.label }}</x-rapidez::summary.title>
                <x-rapidez::summary.value class="text-green-700 font-semibold">- @{{ discount.amount.value | price }}</x-rapidez::summary.value>
            </x-rapidez::summary.row>
        </template>
    </template>

    <x-rapidez::summary.divider />

    <x-rapidez::summary.row>
        <x-rapidez::summary.title class="text-base font-bold">@lang('Total')</x-rapidez::summary.title>
        <x-rapidez::summary.value class="text-base font-bold">
            <template v-if="showTax">@{{ cart.prices.grand_total.value | price }}</template>
            <template v-else>@{{ cart.prices.grand_total.value - cart.taxTotal.value | price }}</template>
        </x-rapidez::summary.value>
    </x-rapidez::summary.row>
</x-rapidez::summary>