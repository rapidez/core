<template v-if="cart.applied_coupons.length" v-for="coupon in cart.applied_coupons">
    <graphql-mutation
        :query="'mutation ($cart_id: String!) { removeCouponFromCart(input: { cart_id: $cart_id }) { cart { ...cart } } } ' + config.fragments.cart"
        :variables="{ cart_id: mask }"
        :callback="updateCart"
        :error-callback="checkResponseForExpiredCart"
        v-slot="{ mutate }"
    >
        <div class="flex">
            <button v-on:click="mutate" data-testid="remove-coupon">
                <x-heroicon-s-x-mark class="h-4 w-4 text-black-400"/>
            </button>
            @{{ coupon.code }}
        </div>
    </graphql-mutation>
</template>
