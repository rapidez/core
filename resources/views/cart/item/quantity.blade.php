<graphql-mutation
    :query="'mutation ($cart_id: String!, $cart_item_uid: ID, $quantity: Float) { updateCartItems(input: { cart_id: $cart_id, cart_items: [{ cart_item_uid: $cart_item_uid, quantity: $quantity }] }) { cart { ...cart } } } ' + config.fragments.cart"
    :variables="{ cart_id: mask.value, cart_item_uid: item.uid, quantity: item.quantity }"
    :callback="(variables, response) => updateCart(variables, response) && variables.quantity <= 0 ? window.$emit('cart-remove', item) : ''"
    :error-callback="checkResponseForExpiredCart"
    :debounce="500"
    v-slot="{ mutate, variables }"
>
    <div class="flex items-center gap-1">
        <x-rapidez::quantity
            v-on:change="mutate"
            v-model="variables.quantity"
            {{-- No "min" here so you're able to remove by lowering to 0 --}}
            v-bind:step="(item.product?.stock_item?.qty_increments ?? 1)"
            v-bind:max="item.product?.stock_item?.max_sale_qty"
        />
    </div>
</graphql-mutation>
