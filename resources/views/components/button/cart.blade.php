<x-rapidez::button.enhanced {{ $attributes->merge([
    'type' => 'submit',
    'dusk' => 'add-to-cart',
])->class('flex items-center') }}>
    <x-heroicon-o-shopping-cart class="mr-2 h-5 w-5" v-if="!addToCart.adding && !addToCart.added" />
    <x-heroicon-o-arrow-path class="mr-2 h-5 w-5 animate-spin" v-if="addToCart.adding" v-cloak />
    <x-heroicon-o-check class="mr-2 h-5 w-5" v-if="addToCart.added" v-cloak />
    <span v-if="!addToCart.adding && !addToCart.added">@lang('Add to cart')</span>
    <span v-if="addToCart.adding" v-cloak>@lang('Adding')...</span>
    <span v-if="addToCart.added" v-cloak>@lang('Added')</span>
</x-rapidez::button.enhanced>
