<graphql-mutation
    :query="config.queries.cart + ' mutation ($cart_id: String!, $cart_item_id: Int) { removeItemFromCart(input: { cart_id: $cart_id, cart_item_id: $cart_item_id }) { cart { ...cart } } }'"
    :variables="{ cart_id: mask, cart_item_id: item.id }"
    :notify="{ message: item.product.name+' '+config.translations.cart.remove }"
    :callback="(variables, response) => updateCart(variables, response) && window.app.$emit('cart-remove', item)"
    :error-callback="checkResponseForExpiredCart"
    v-slot="{ mutate }"
>
    <button :disabled="$root.loading" v-on:click="mutate" title="@lang('Remove')" :dusk="'item-delete-'+index" class="hover:underline">
        @lang('Remove')
    </button>
</graphql-mutation>
