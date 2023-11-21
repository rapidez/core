<add-to-cart v-bind:product="item" v-slot="addToCart" v-cloak>
    <form class="px-2 pb-2" v-on:submit.prevent="addToCart.add">
        <div class="flex items-center space-x-2 mb-2">
            <div class="font-semibold">
                @{{ (addToCart.simpleProduct.special_price || addToCart.simpleProduct.price) | price }}
            </div>
            <div class="line-through text-sm" v-if="addToCart.simpleProduct.special_price">
                @{{ addToCart.simpleProduct.price | price }}
            </div>
        </div>

        <p v-if="!item.in_stock" class="text-red-600 text-xs">@lang('Sorry! This product is currently out of stock.')</p>
        <div v-else>
            @include('rapidez::listing.partials.item.super_attributes')

            <x-rapidez::button type="submit" class="flex items-center" dusk="add-to-cart">
                <x-heroicon-o-shopping-cart class="h-5 w-5 mr-2" v-if="!addToCart.adding && !addToCart.added" />
                <x-heroicon-o-arrow-path class="h-5 w-5 mr-2 animate-spin" v-if="addToCart.adding" />
                <x-heroicon-o-check class="h-5 w-5 mr-2" v-if="addToCart.added" />
                <span v-if="!addToCart.adding && !addToCart.added">@lang('Add to cart')</span>
                <span v-if="addToCart.adding">@lang('Adding')...</span>
                <span v-if="addToCart.added">@lang('Added')</span>
            </x-rapidez::button>
        </div>
    </form>
</add-to-cart>
