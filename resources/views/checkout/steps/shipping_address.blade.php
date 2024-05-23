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
    {{--
    TODO: Create a "mutate on safe" prop so we don't need forms here and have one wrapping everything?
    Or we should run the validation of all forms when clicking on the next button?
    --}}
>
    <form v-on:change="mutate">
        @include('rapidez::checkout.partials.address', ['type' => 'shipping'])
    </form>
</graphql-mutation>
