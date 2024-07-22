<dl class="mb-5 flex w-full flex-col rounded-lg border [&>*]:flex [&>*]:flex-wrap [&>*]:justify-between [&>*]:p-3 [&>*]:border-b [&>*:last-child]:border-none">
    <div>
        <dt>@lang('Subtotal')</dt>
        <dd>@{{ cart.prices.subtotal_including_tax.value | price }}</dd>
    </div>
    <div v-if="cart.prices.applied_taxes.length">
        <dt>@lang('Tax')</dt>
        <dd>@{{ cart.prices.applied_taxes[0].amount.value | price }}</dd>
    </div>
    <div v-for="value, name in cart.fixedProductTaxes.value">
        <dt>@{{ name }}</dt>
        <dd>@{{ value | price }}</dd>
    </div>
    <div v-if="cart.shipping_method">
        <dt>
            @lang('Shipping')<br>
            <small>@{{ cart.shipping_method.carrier_title }} - @{{ cart.shipping_method.method_title }}</small>
        </dt>
        <dd>@{{ cart.shipping_method.amount.value | price }}</dd>
    </div>
    <div v-for="discount in cart.prices.discounts">
        <dt>@{{ discount.label }}</dt>
        <dd>-@{{ discount.amount.value | price }}</dd>
    </div>
    <div class="font-bold">
        <dt>@lang('Total')</dt>
        <dd>
            @{{ cart.prices.subtotal_including_tax.value + cart.shipping_method.amount.value === cart.prices.grand_total.value
            ? cart.prices.grand_total.value
            : cart.prices.grand_total.value + cart.shipping_method.amount.value | price }}
        </dd>
    </div>
</dl>

<x-rapidez::button href="{{ route('checkout') }}" dusk="checkout" class="w-full text-center">
    @lang('Checkout')
</x-rapidez::button>
