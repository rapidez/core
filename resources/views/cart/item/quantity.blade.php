<graphql-mutation
    :query="config.queries.cart + ' mutation ($cart_id: String!, $cart_item_id: Int, $quantity: Float) { updateCartItems(input: { cart_id: $cart_id, cart_items: [{ cart_item_id: $cart_item_id, quantity: $quantity }] }) { cart { ...cart } } }'"
    :variables="{ cart_id: mask, cart_item_id: item.id, quantity: item.quantity }"
    :callback="updateCart"
    :error-callback="checkResponseForExpiredCart"
    v-slot="{ mutate, variables }"
>
    <form v-on:submit.prevent="mutate" class="flex items-center gap-1">
        <x-rapidez::input
            name="qty"
            type="number"
            :label="false"
            v-model="variables.quantity"
            v-bind:dusk="'qty-'+index"
            {{-- TODO: We don't need these importants with Tailwind merge and center isn't really center with type number --}}
            class="!w-14 !px-1 text-center"

            ::min="Math.max(item.product.stock_item?.min_sale_qty, item.product.stock_item?.qty_increments) || null"
            ::max="item.product.stock_item?.max_sale_qty"
            ::step="item.product.stock_item?.qty_increments"
        />
        <x-rapidez::button type="submit" v-bind:dusk="'item-update-'+index" :title="__('Update')">
            <x-heroicon-s-arrow-path class="h-4 w-4" />
        </x-rapidez::button>
    </form>
</graphql-mutation>
