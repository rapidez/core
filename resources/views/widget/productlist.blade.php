<reactive-base v-cloak :app="config.es_prefix + '_products_' + config.store" :url="config.es_url">
    <reactive-list
        component-id="productlist"
        data-field="id"
        list-class="flex flex-wrap mt-5 -mx-1 overflow-hidden"
        :size="{{ $options['products_count'] }}"
        :show-result-stats="false"
        :default-query="function () { return { query: { terms: { '{{ $condition->attribute }}.keyword': ['{{ implode("','", explode(', ', $condition->value)) }}'] } } } }"
    >
        @include('rapidez::category.partials.listing.item')
    </reactive-list>
</reactive-base>
