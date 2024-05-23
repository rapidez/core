<graphql-mutation
    :query="config.queries.placeOrder"
    :variables="{ cart_id: mask }"
    {{-- :callback="storeOrderInLocalstorageAndRedirectToSuccessPage" --}}
    {{-- :error-callback="alert" --}}
    v-slot="{ mutate, variables }"
>
    <form v-on:submit.prevent="mutate">
        <x-rapidez::button type="submit">
            @lang('Order')
        </x-rapidez::button>
    </form>
</graphql-mutation>
