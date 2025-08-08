<x-rapidez::button.conversion {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'relative',
        'data-testid' => 'add-to-cart',
    ]) }}
    v-bind:class="{'button-loading': addToCart.adding}"
>
    <x-heroicon-o-shopping-cart class="size-5" />
    <span>@lang('Add to cart')</span>
</x-rapidez::button.conversion>
