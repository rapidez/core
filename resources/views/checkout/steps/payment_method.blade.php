<graphql-mutation
    :query="config.queries.setPaymentMethodOnCart"
    :variables="{
        cart_id: mask,
        code: cart.selected_payment_method.code,
    }"
    group="payment"
    :before-request="handleBeforePaymentMethodHandlers"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    mutate-event="setPaymentMethodOnCart"
    v-slot="{ mutate, variables }"
>
    <div data-function="mutate">
        <template v-for="(method, index) in cart.available_payment_methods">
            <template v-if="false"></template>
            @stack('payment_methods')
            <template v-else>
                <x-rapidez::radio
                    name="payment_method"
                    v-model="variables.code"
                    v-bind:value="method.code"
                    v-bind:dusk="'method-'+index"
                    v-on:change="mutate"
                    required
                >
                    @{{ method.title }}
                </x-rapidez::radio>
            </template>
        </template>
    </div>
</graphql-mutation>

