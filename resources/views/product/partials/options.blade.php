<graphql query='@include('rapidez::product.partials.queries.options', ['sku' => $product->sku])' :callback="setProductOptions" v-cloak>
    <div v-if="data" slot-scope="{ data }" class="flex flex-col space-y-3">
        <div v-for="option in Object.values(data.products.items[0].options).sort((a, b) => a.sort_order - b.sort_order)">
            @include('rapidez::product.partials.options.field')
            @include('rapidez::product.partials.options.area')
            @include('rapidez::product.partials.options.dropdown')
            @include('rapidez::product.partials.options.file')
        </div>
    </div>
</graphql>

