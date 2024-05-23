<graphql-mutation
    :query="config.queries.setBillingAddressOnCart"
    :variables="{
        cart_id: mask,
        ...window.address_defaults,
        ...cart.billing_address,
        country_code: cart.billing_address?.country.code || window.address_defaults.country_code
    }"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    mutate-event="setBillingAddressOnCart"
    v-slot="{ mutate, variables }"
>
    <form v-on:change="mutate">
        {{-- TODO: Same problem as in the sidebar; how do we know it was previously selected? --}}
        <x-rapidez::checkbox v-model="variables.same_as_shipping">
            @lang('My billing and shipping address are the same')
        </x-rapidez::checkbox>

        <div v-if="!variables.same_as_shipping">
            @include('rapidez::checkout.partials.address', ['type' => 'billing'])
        </div>
    </form>
</graphql-mutation>
