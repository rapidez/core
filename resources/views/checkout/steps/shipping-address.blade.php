<graphql-mutation
    :query="config.queries.setNewShippingAddressesOnCart"
    :variables="{
        cart_id: mask.value,
        ...window.address_defaults,
        ...cart.value.shipping_addresses?.[0],
        country_code: cart.value.shipping_addresses?.[0]?.country.code || window.address_defaults.country_code,
        region_id: cart.value.shipping_addresses?.[0]?.region.region_id || window.address_defaults.region_id,
    }"
    group="shipping"
    {{-- Saving the shipping address resets same_as_billing in Magento, so the billing address is copied again within the same mutation. --}}
    {{-- Without a billing address on the cart yet we follow the checkbox of the billing address step. --}}
    :before-request="(query, variables, options) => [
        variables.customer_address_id ? config.queries.setExistingShippingAddressesOnCart : query,
        { ...variables, same_as_billing: cart.value.billing_address ? cart.value.billing_address.same_as_shipping : (refs.billingAddress?.data.same_as_shipping ?? true) },
        options,
    ]"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    mutate-event="setShippingAddressesOnCart"
    v-slot="{ mutate, variables }"
    v-if="!cart.value.is_virtual"
    key="checkout-shipping-address"
>
    <fieldset partial-submit v-on:partial-submit="(e) => mutate().then(e.detail.resolve).catch(e.detail.reject)" v-on:change="function (e) {e.target.closest('fieldset').querySelector(':invalid') === null && mutate()}">
        @include('rapidez::checkout.partials.address', ['type' => 'shipping'])
    </fieldset>
</graphql-mutation>
