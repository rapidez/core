{{--
This is the default add to cart button and has a nice loading animation when you click on it.

Example:
```
    <x-rapidez:button.card>Something</x-rapidez:button.card>
```
--}}

<x-rapidez::button.enhanced
    v-bind:class="{ 'button-loading': addToCart.adding }"
    class="flex-1"
    {{ $attributes->merge([ 'type' => 'submit', 'dusk' => 'add-to-cart']) }}
>
    <x-heroicon-o-shopping-cart class="mr-2 size-5" />
    <span v-if="!addToCart.adding && !addToCart.added">@lang('Add to cart')</span>
    <span v-if="addToCart.adding" v-cloak>@lang('Adding')</span>
    <span v-if="addToCart.added" v-cloak>@lang('Added')</span>
</x-rapidez::button.enhanced>
