<graphql-mutation
    :query="'mutation ($cart_id: String!, $cart_item_id: Int, $quantity: Float) { updateCartItems(input: { cart_id: $cart_id, cart_items: [{ cart_item_id: $cart_item_id, quantity: $quantity }] }) { cart { ' + config.queries.cart + ' } } }'"
    :variables="{ cart_id: window.app.mask, cart_item_id: item.id, quantity: item.quantity }"
    :callback="refreshCart"
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
            {{-- TODO: We can't get these values with GraphQL --}}
            {{-- ::min="item.min_sale_qty > item.qty_increments ? item.min_sale_qty : item.qty_increments" --}}
            {{-- ::step="item.qty_increments" --}}
        />
        <x-rapidez::button type="submit" v-bind:dusk="'item-update-'+index" :title="__('Update')">
            <x-heroicon-s-arrow-path class="h-4 w-4" />
        </x-rapidez::button>
    </form>
</graphql-mutation>
