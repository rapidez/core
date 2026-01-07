<graphql-mutation
    :query="config.queries.setNewBillingAddressOnCart"
    :variables="JSON.parse(JSON.stringify({
        cart_id: mask,
        ...window.address_defaults,
        ...cart.billing_address,
        same_as_shipping: !cart.is_virtual && (cart?.billing_address?.same_as_shipping ?? true),
        country_code: cart.billing_address?.country.code || window.address_defaults.country_code,
        region_id: cart.billing_address?.region.region_id || window.address_defaults.region_id,
    }))"
    :watch-ignore="['same_as_shipping']"
    :before-request="(query, variables, options) => [variables.customer_address_id ? config.queries.setExistingBillingAddressOnCart : query, variables, options]"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    group="billing"
    mutate-event="setBillingAddressOnCart"
    v-on:change="function (e) {
        e.target.closest('fieldset').querySelector(':invalid') === null
        && (!e.variables.same_as_shipping || !!cart?.shipping_addresses?.[0]?.postcode)
        && window.app.$emit('setBillingAddressOnCart')
    }"
    v-slot="{ mutate, variables }"
>
    <fieldset partial-submit="mutate">
        <template v-if="!cart.is_virtual">
            <x-rapidez::input.checkbox v-model="variables.same_as_shipping">
                @lang('My billing and shipping address are the same')
            </x-rapidez::input.checkbox>
        </template>
        <template v-if="!variables.same_as_shipping">
            @include('rapidez::checkout.partials.address', ['type' => 'billing'])
        </template>
    </fieldset>
</graphql-mutation>
