<graphql-mutation
    :query="config.queries.setPaymentMethodOnCart"
    :variables="{
        cart_id: mask,
        code: cart.selected_payment_method.code,
    }"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    v-slot="{ mutate, variables }"
>
    <div>
        <template v-for="(method, index) in cart.available_payment_methods">
            <x-rapidez::radio
                v-model="variables.code"
                v-bind:value="method.code"
                v-bind:dusk="'method-'+index"
                v-on:change="mutate"
            >
                @{{ method.title }}
            </x-rapidez::radio>
        </template>
    </div>
</graphql-mutation>

