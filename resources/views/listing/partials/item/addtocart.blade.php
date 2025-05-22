<form v-on:submit.prevent="addToCart.add">
    <div class="flex items-center gap-x-2 mt-1">
        <div class="font-semibold text-lg">
            @{{ (addToCart.simpleProduct.special_price || addToCart.simpleProduct.price) | price }}
        </div>
        <div class="line-through text-sm" v-if="addToCart.simpleProduct.special_price">
            @{{ addToCart.simpleProduct.price | price }}
        </div>
    </div>

    <p v-if="!item.in_stock" class="text-red-600 text-xs">
        @lang('Sorry! This product is currently out of stock.')
    </p>
    <div v-else>
        @includeWhen(Rapidez::config('catalog/frontend/show_swatches_in_product_list'), 'rapidez::listing.partials.item.super_attributes')

        <x-rapidez::button.cart class="mt-4" />
    </div>
</form>
