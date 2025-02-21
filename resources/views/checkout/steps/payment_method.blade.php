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
    <div class="flex flex-col gap-3" partial-submit="mutate">
        <label class="flex items-center p-5 border rounded-sm relative bg-white" v-if="!cart.is_virtual && !cart.shipping_addresses[0]?.uid">
            <span>@lang('Please enter a shipping address first')</span>
        </label>
        <label class="flex items-center p-5 border rounded-sm relative bg-white cursor-pointer" v-else v-for="(method, index) in cart.available_payment_methods">
            <template v-if="false"></template>
            @stack('payment_methods')
            <template v-else>
                <x-rapidez::input.radio.base
                    name="payment_method"
                    v-model="variables.code"
                    v-bind:value="method.code"
                    v-bind:dusk="'payment-method-'+index"
                    v-on:change="mutate"
                    required
                />
                <span class="flex items-center text-sm text-neural">
                    <span class="ml-2.5">@{{ method.title }}</span>
                    <img
                        class="absolute right-5 size-8"
                        v-bind:src="`/payment-icons/${method.code}.svg`"
                        onerror="this.onerror=null; this.src=`/payment-icons/default.svg`"
                    >
                </span>
            </template>
        </label>
    </div>
</graphql-mutation>

