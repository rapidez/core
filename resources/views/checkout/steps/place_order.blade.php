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
        <x-rapidez::button.enhanced type="submit" dusk="continue" class="mt-3">
            @lang('Place order')
        </x-rapidez::button.enhanced>
    </fieldset>
</graphql-mutation>
