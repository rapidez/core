<form class="px-2 pb-2 mt-auto" v-on:submit.prevent="addToCart.add">
    <div class="flex items-center space-x-2 mb-2">
        <div class="font-semibold">
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

        <x-rapidez::button.cart/>
    </div>
</form>
