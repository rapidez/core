<graphql-mutation
    :query="config.queries.setNewShippingAddressesOnCart"
    :variables="{
        cart_id: mask,
        ...window.address_defaults,
        ...cart.shipping_addresses[0],
        country_code: cart.shipping_addresses[0]?.country.code || window.address_defaults.country_code
    }"
    group="shipping"
    :before-request="(query, variables, options) => [variables.customer_address_id ? config.queries.setExistingShippingAddressesOnCart : query, variables, options]"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    :watch="false"
    mutate-event="setShippingAddressesOnCart"
    v-slot="{ mutate, variables }"
    v-if="!cart.is_virtual"
>
    <fieldset data-function="mutate" v-on:change="function (e) {e.target.closest('fieldset').querySelector(':invalid') === null && mutate().then(() => (cart?.billing_address?.same_as_shipping ?? true) && window.app.$emit('setBillingAddressOnCart'))}">
        @include('rapidez::checkout.partials.address', ['type' => 'shipping'])
    </fieldset>
</graphql-mutation>
