<graphql-mutation
    :query="'mutation ($cart_id: String!, $cart_item_id: Int) { removeItemFromCart(input: { cart_id: $cart_id, cart_item_id: $cart_item_id }) { cart { ...cart } } } ' + config.fragments.cart"
    :variables="{ cart_id: mask.value, cart_item_id: item.id }"
    :notify="{ message: item.product.name+' '+config.translations.cart.remove }"
    :callback="(variables, response) => updateCart(variables, response) && window.$emit('cart-remove', item)"
    :error-callback="checkResponseForExpiredCart"
    v-slot="{ mutate }"
>
    <x-rapidez::button.link
        v-on:click="mutate"
        title="@lang('Remove')"
        class="text-red-700"
        v-bind:disabled="loading.value"
        data-testid="cart-item-remove"
    >
        @lang('Remove')
    </x-rapidedz::button.link>
</graphql-mutation>
