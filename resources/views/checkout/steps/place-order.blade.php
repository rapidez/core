<graphql-mutation
    :query="config.queries.placeOrder"
    :variables="{ cart_id: mask.value }"
    :before-request="handleBeforePlaceOrderHandlers"
    :callback="handlePlaceOrder"
    mutate-event="placeOrder"
    redirect="{{ route('checkout.success') }}"
    v-slot="{ mutate, variables }"
>
    <fieldset>
        <x-rapidez::button.conversion type="submit" data-testid="continue" class="mt-3" loader v-on:mousedown.prevent="{{-- Do not remove, this prevents requiring double click for the submit action --}}">
            @lang('Place order')
        </x-rapidez::button.conversion>
    </fieldset>
</graphql-mutation>
