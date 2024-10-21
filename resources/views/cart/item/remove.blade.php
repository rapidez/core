<graphql-mutation
    :query="'mutation ($cart_id: String!, $cart_item_id: Int) { removeItemFromCart(input: { cart_id: $cart_id, cart_item_id: $cart_item_id }) { cart { ' + config.queries.cart + ' } } }'"
    :variables="{ cart_id: mask, cart_item_id: item.id }"
    :notify="{ message: item.product.name+' '+config.translations.cart.remove }"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    v-slot="cartItemRemoveQueryScope"
>
    <button :disabled="$root.loading" v-on:click="cartItemRemoveQueryScope.mutate" title="@lang('Remove')" :dusk="'item-delete-'+index" class="hover:underline">
        @lang('Remove')
    </button>
</graphql-mutation>
