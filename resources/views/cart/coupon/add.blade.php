<graphql-mutation
    :query="'mutation ($cart_id: String!, $coupon_code: String!) { applyCouponToCart(input: { cart_id: $cart_id, coupon_code: $coupon_code }) { cart { ...cart } } } ' + config.fragments.cart"
    :variables="{ cart_id: mask, coupon_code: '' }"
    :notify="{ message: config.translations.cart.coupon.applied }"
    :clear="true"
    :watch="false"
    :callback="updateCart"
    v-slot="{ mutate, variables }"
>
    <form v-on:submit.prevent="mutate" class="flex gap-3">
        <x-rapidez::input
            name="couponCode"
            placeholder="Coupon code"
            v-model="variables.coupon_code"
            v-bind:disabled="$root.loading"
            required
        />
        <x-rapidez::button.outline type="submit" class="sm:text-sm" data-testid="apply-coupon">
            @lang('Apply')
        </x-rapidez::button.outline>
    </form>
</graphql-mutation>
