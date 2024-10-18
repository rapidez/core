<graphql-mutation
    :query="'mutation ($cart_id: String!, $cart_item_id: Int, $quantity: Float) { updateCartItems(input: { cart_id: $cart_id, cart_items: [{ cart_item_id: $cart_item_id, quantity: $quantity }] }) { cart { ' + config.queries.cart + ' } } }'"
    :variables="{ cart_id: mask, cart_item_id: item.id, quantity: item.quantity }"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    v-slot="{ mutate, variables }"
    mutate-event="updated-quantity"
>
    <div class="flex items-center gap-1">
        <x-rapidez::quantity index="item.id" model="variables.quantity" minSaleQty="(item.product.stock_item?.min_sale_qty ?? 1)" qtyIncrements="(item.product.stock_item?.qty_increments ?? 1)"/>
    </form>
</graphql-mutation>
