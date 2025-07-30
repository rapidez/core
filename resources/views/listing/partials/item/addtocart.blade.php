<form v-on:submit.prevent="addToCart.add">
    <p v-if="!item.in_stock" class="text-red-600 text-xs">
        @lang('Sorry! This product is currently out of stock.')
    </p>
    <div v-else>
        @includeWhen(Rapidez::config('catalog/frontend/show_swatches_in_product_list'), 'rapidez::listing.partials.item.super_attributes')

        <div class="flex flex-wrap items-center gap-4 mt-4">
            <x-rapidez::button.cart />

            @if (App::providerIsLoaded('Rapidez\Wishlist\WishlistServiceProvider'))
                @include('rapidez::wishlist.button')
            @endif
        </div>
    </div>
</form>


