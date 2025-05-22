<form v-on:submit.prevent="addToCart.add">
    <p v-if="!item.in_stock" class="text-red-600 text-xs">
        @lang('Sorry! This product is currently out of stock.')
    </p>
    <div v-else>
        @includeWhen(Rapidez::config('catalog/frontend/show_swatches_in_product_list'), 'rapidez::listing.partials.item.super_attributes')

        <x-rapidez::button.cart class="mt-4" />
    </div>
</form>
