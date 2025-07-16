<x-rapidez::button.conversion {{ $attributes->merge([
        'type' => 'submit',
        'dusk' => 'add-to-cart',
        'class' => 'relative',
    ]) }}
    v-bind:class="{'button-loading': addToCart.adding}"
>
    <x-heroicon-o-shopping-cart class="size-5" />
    <span>@lang('Add to cart')</span>
</x-rapidez::button.enhanced>
