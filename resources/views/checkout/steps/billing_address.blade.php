<graphql-mutation
    :query="config.queries.setNewBillingAddressOnCart"
    :variables="JSON.parse(JSON.stringify({
        cart_id: mask,
        ...window.address_defaults,
        ...cart.billing_address,
        same_as_shipping: !cart.is_virtual && (cart?.billing_address?.same_as_shipping ?? true),
        country_code: cart.billing_address?.country.code || window.address_defaults.country_code
    }))"
    :before-request="(query, variables, options) => [variables.customer_address_id ? config.queries.setExistingBillingAddressOnCart : query, variables, options]"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    :watch="false"
    group="billing"
    mutate-event="setBillingAddressOnCart"
    v-slot="{ mutate, variables }"
>
    <div partial-submit="mutate">
        <template v-if="!cart.is_virtual">
            <x-rapidez::checkbox v-model="variables.same_as_shipping" v-on:change="window.app.$emit('setBillingAddressOnCart')">
                @lang('My billing and shipping address are the same')
            </x-rapidez::checkbox>
        </template>

        <fieldset v-if="!variables.same_as_shipping" v-on:change="window.app.$emit('setBillingAddressOnCart')">
            @include('rapidez::checkout.partials.address', ['type' => 'billing'])
        </fieldset>
    </div>
</graphql-mutation>
