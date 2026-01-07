<graphql-mutation
    :query="'mutation ($cart_id: String!, $cart_item_id: Int) { removeItemFromCart(input: { cart_id: $cart_id, cart_item_id: $cart_item_id }) { cart { ...cart } } } ' + config.fragments.cart"
    :variables="{ cart_id: mask, cart_item_id: item.id }"
    :notify="{ message: item.product.name+' '+config.translations.cart.remove }"
    :callback="(variables, response) => updateCart(variables, response) && window.app.$emit('cart-remove', item)"
    :error-callback="checkResponseForExpiredCart"
    v-slot="{ mutate }"
>
    <button
        v-on:click="mutate"
        title="@lang('Remove')"
        class="hover:underline text-red-700"
        :disabled="$root.loading"
        data-testid="cart-item-remove"
    >
        @lang('Remove')
    </button>
</graphql-mutation>
