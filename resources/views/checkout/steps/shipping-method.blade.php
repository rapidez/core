<graphql-mutation
    :query="config.queries.setShippingMethodsOnCart"
    :variables="{
        cart_id: mask,
        method: cart.shipping_addresses?.[0]?.selected_shipping_method?.carrier_code+'/'+cart.shipping_addresses?.[0]?.selected_shipping_method?.method_code,
    }"
    group="shipping"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    :before-request="function (query, variables, options) {
        variables.carrier_code = variables.method.split('/')[0]
        variables.method_code = variables.method.split('/')[1]
        return [query, variables, options]
    }"
    mutate-event="setShippingMethodsOnCart"
    v-slot="{ mutate, variables }"
    v-if="!cart.is_virtual"
>
    <fieldset class="flex flex-col gap-3" partial-submit="mutate" v-on:change="window.app.$emit('setShippingAddressesOnCart')">
        <label class="flex items-center flex-wrap p-5 border rounded relative bg-white" v-if="!cart.shipping_addresses?.[0]?.uid">
            <span>@lang('Please enter a shipping address first')</span>
        </label>
        <label class="flex gap-x-1.5 p-5 border rounded bg-white cursor-pointer text-sm text has-[[disabled]]:cursor-not-allowed" v-for="(method, index) in cart.shipping_addresses?.[0]?.available_shipping_methods">
            <template v-if="false"></template>
                @stack('shipping_methods')
            <template v-else>
                <x-rapidez::input.radio.base
                    name="shipping_method"
                    v-model="variables.method"
                    v-bind:value="method.carrier_code+'/'+method.method_code"
                    v-bind:disabled="!method.available"
                    data-testid="shipping-method"
                    v-on:change="mutate"
                    required
                    class="mr-1"
                />
                <span class="flex flex-col">
                    <span>
                        <span v-if="method.method_title" class="font-medium">
                            @{{ method.method_title }}
                        </span>
                        <span v-else class="font-medium">
                            @{{ method.carrier_title }}
                        </span>
                        <span v-if="method.amount.value"> - @{{ method.amount.value | price }}</span>
                    </span>
                    <span class="basis-full text-muted" v-if="method.error_message" v-text="method.error_message"></span>
                </span>
            </template>
        </label>
    </fieldset>
</graphql-mutation>
