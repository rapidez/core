<template v-if="cart.applied_coupons.length" v-for="coupon in cart.applied_coupons">
    <graphql-mutation
        :query="'mutation ($cart_id: String!) { removeCouponFromCart(input: { cart_id: $cart_id }) { cart { ' + config.queries.cart + ' } } }'"
        :variables="{ cart_id: mask }"
        :callback="updateCart"
        :error-callback="checkResponseForExpiredCart"
        v-slot="couponListQueryScope"
    >
        <div class="flex">
            <button v-on:click="couponListQueryScope.mutate" v-bind:dusk="'remove-coupon-' + coupon.code">
                <x-heroicon-s-x-mark class="h-4 w-4 text-black-400"/>
            </button>
            @{{ coupon.code }}
        </div>
    </graphql-mutation>
</template>
