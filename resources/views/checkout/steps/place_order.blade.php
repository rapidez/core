<graphql-mutation
    :query="config.queries.placeOrder"
    :variables="{ cart_id: mask }"
    :before-request="handleBeforePlaceOrderHandlers"
    :callback="handlePlaceOrder"
    mutate-event="placeOrder"
    redirect="{{ route('checkout.success') }}"
    {{-- :error-callback="alert" --}}
    v-slot="{ mutate, variables }"
>
    <fieldset>
        <x-rapidez::button type="submit">
            @lang('Order')
        </x-rapidez::button>
    </fieldset>
</graphql-mutation>
