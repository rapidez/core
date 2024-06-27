<graphql-mutation
    :query="config.queries.placeOrder"
    :variables="{ cart_id: mask }"
    {{-- :callback="storeOrderInLocalstorageAndRedirectToSuccessPage" --}}
    mutate-event="placeOrder"
    {{-- :error-callback="alert" --}}
    v-slot="{ mutate, variables }"
>
    <fieldset>
        <x-rapidez::button type="submit">
            @lang('Order')
        </x-rapidez::button>
    </fieldset>
</graphql-mutation>
