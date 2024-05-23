{{--
TODO: This is only for guests, when we're logged in we could
just display the email address with a logout link. We
should also check for account existence here!
--}}
<graphql-mutation
    :query="config.queries.setGuestEmailOnCart"
    :variables="{
        cart_id: mask,
        email: cart.email,
    }"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    v-slot="{ mutate, variables }"
>
    <x-rapidez::input
        label="Email"
        name="email"
        v-model="variables.email"
        v-on:change="mutate"
        required
    />
</graphql-mutation>

