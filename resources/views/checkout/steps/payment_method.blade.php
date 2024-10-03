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
    <div class="flex flex-col gap-3" data-function="mutate">
        <div class="p-5 border rounded relative" v-for="(method, index) in cart.available_payment_methods">
            <template v-if="false"></template>
            @stack('payment_methods')
            <template v-else>
                <x-rapidez::radio
                    name="payment_method"
                    v-model="variables.code"
                    v-bind:value="method.code"
                    v-bind:dusk="'payment-method-'+index"
                    v-on:change="mutate"
                    required
                >
                    <div class="flex items-center">
                        <span>@{{ method.title }}</span>
                        <img
                            class="absolute right-5 w-8 h-8"
                            v-bind:src="`/payment-icons/${method.code}.svg`"
                            onerror="this.onerror=null; this.src=`/payment-icons/default.svg`"
                        >
                    </div>
                </x-rapidez::radio>
            </template>
        </div>
    </div>
</graphql-mutation>

