<recently-viewed :max="{{ $options->page_size ?? $max ?? Rapidez::config('catalog/recently_products/viewed_count') }}" v-slot="{ products }">
    <x-rapidez::productlist
        title="Recently viewed"
        field="entity_id"
        value="products"
    />
</recently-viewed>
