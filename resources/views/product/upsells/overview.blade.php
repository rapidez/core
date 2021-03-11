<graphql v-cloak query='@include('rapidez::product.queries.UpSells', ['sku' => $product->sku])'>
    <div slot-scope="{ data }" v-if="data && data.products.items[0].upsell_products" class="flex flex-wrap mt-5 -mx-1 overflow-hidden">
        <div class="flex w-1/2 sm:w-1/3 lg:w-1/4 px-1 my-1" v-for="product in data.products.items[0].upsell_products">
            @include('rapidez::product.upsells.partials.item')
        </div>
    </div>
</graphql>
