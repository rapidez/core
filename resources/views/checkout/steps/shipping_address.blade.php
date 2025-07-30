<graphql-mutation
    :query="config.queries.setNewShippingAddressesOnCart"
    :variables="{
        cart_id: mask.value,
        ...window.address_defaults,
        ...cart.value.shipping_addresses[0],
        country_code: cart.value.shipping_addresses[0]?.country.code || window.address_defaults.country_code,
        region_id: cart.value.shipping_addresses[0]?.region.region_id || window.address_defaults.region_id,
    }"
    group="shipping"
    :before-request="(query, variables, options) => [variables.customer_address_id ? config.queries.setExistingShippingAddressesOnCart : query, variables, options]"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    mutate-event="setShippingAddressesOnCart"
    v-slot="{ mutate, variables }"
    v-if="!cart.value.is_virtual"
    key="checkout-shipping-address"
>
    <fieldset partial-submit v-on:partial-submit="async () => await mutate()" v-on:change="function (e) {e.target.closest('fieldset').querySelector(':invalid') === null && mutate().then(() => (cart?.billing_address?.same_as_shipping ?? true) && window.$emit('setBillingAddressOnCart'))}">
        @include('rapidez::checkout.partials.address', ['type' => 'shipping'])
    </fieldset>
</graphql-mutation>
