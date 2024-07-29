<graphql-mutation
    :query="'mutation ($cart_id: String!, $coupon_code: String!) { applyCouponToCart(input: { cart_id: $cart_id, coupon_code: $coupon_code }) { cart { ' + config.queries.cart + ' } } }'"
    :variables="{ cart_id: mask, coupon_code: '' }"
    :notify="{ message: config.translations.cart.coupon.applied }"
    :clear="true"
    :watch="false"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    v-slot="{ mutate, variables }"
>
    <form v-on:submit.prevent="mutate" class="flex gap-3">
        <x-rapidez::input.base
            name="couponCode"
            placeholder="Coupon code"
            v-model="variables.coupon_code"
            v-bind:disabled="$root.loading"
            required
        />
        <x-rapidez::button type="submit" class="sm:text-sm">
            @lang('Apply')
        </x-rapidez::button>
    </form>
</graphql-mutation>
