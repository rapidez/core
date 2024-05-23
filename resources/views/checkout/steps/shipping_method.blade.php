<graphql-mutation
    :query="config.queries.setShippingMethodsOnCart"
    :variables="{
        cart_id: mask,
        method: cart.shipping_addresses[0]?.selected_shipping_method?.carrier_code+'/'+cart.shipping_addresses[0]?.selected_shipping_method?.method_code,
    }"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    :before-request="function (query, variables, options) {
        variables.carrier_code = variables.method.split('/')[0]
        variables.method_code = variables.method.split('/')[1]
        return [query, variables, options]
    }"
    v-slot="{ mutate, variables }"
>
    <div>
        <template v-for="(method, index) in cart.shipping_addresses[0]?.available_shipping_methods">
            <x-rapidez::radio
                v-model="variables.method"
                v-bind:value="method.carrier_code+'/'+method.method_code"
                v-bind:disabled="!method.available"
                v-bind:dusk="'method-'+index"
                v-on:change="mutate"
            >
                @{{ method.method_title }}
                <span v-if="method.amount.value">- @{{ method.amount.value | price }}</span>
            </x-rapidez::radio>
        </template>
    </div>
</graphql-mutation>

