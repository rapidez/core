<graphql-mutation
    :query="config.queries.placeOrder"
    :variables="{ cart_id: mask }"
    :before-request="handleBeforePlaceOrderHandlers"
    :callback="handlePlaceOrder"
    mutate-event="placeOrder"
    redirect="{{ route('checkout.success') }}"
    v-slot="{ mutate, variables }"
>
    <fieldset>
        <x-rapidez::button.conversion type="submit" dusk="continue" class="mt-3">
            @lang('Place order')
        </x-rapidez::button.conversion>
    </fieldset>
</graphql-mutation>
