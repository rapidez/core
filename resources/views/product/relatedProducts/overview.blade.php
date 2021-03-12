<graphql v-cloak query='@include('rapidez::product.queries.relatedProducts', ['sku' => $product->sku])'>
    <div class="mt-10" slot-scope="{ data }" v-if="data && data.products.items[0].related_products.length">
        <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <span class="text-2xl leading-6 font-medium text-gray-900">
                @lang('Related products')
            </span>
        </div>
        <div class="flex flex-wrap mt-5 -mx-1 overflow-hidden">
            <div class="flex w-1/2 sm:w-1/3 lg:w-1/4 px-1 my-1" v-for="product in data.products.items[0].related_products">
                @include('rapidez::product.partials.product-item')
            </div>
        </div>
    </div>
</graphql>
