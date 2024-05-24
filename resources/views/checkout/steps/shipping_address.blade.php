<graphql-mutation
    :query="config.queries.setShippingAddressesOnCart"
    :variables="{
        cart_id: mask,
        ...window.address_defaults,
        ...cart.shipping_addresses[0],
        country_code: cart.shipping_addresses[0]?.country.code || window.address_defaults.country_code
    }"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    mutate-event="setShippingAddressesOnCart"
    v-slot="{ mutate, variables }"
>
    <div>
        @include('rapidez::checkout.partials.address', ['type' => 'shipping'])
    </div>
</graphql-mutation>
