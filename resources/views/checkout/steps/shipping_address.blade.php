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
    mutate-event="setShippingAddressesOnCart"
    v-slot="{ mutate, variables }"
>
    <fieldset data-function="mutate" v-on:change="window.app.$emit('setShippingAddressesOnCart') && window.app.$emit('setBillingAddressOnCart')">
        @include('rapidez::checkout.partials.address', ['type' => 'shipping'])
    </fieldset>
</graphql-mutation>
