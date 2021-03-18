@if(isset($productIds))
    <reactive-base v-cloak :app="config.es_prefix + '_products_' + config.store" :url="config.es_url">
        <reactive-list
            component-id="{{ $code }}"
            data-field="id"
            list-class="flex flex-wrap mt-5 -mx-1 overflow-hidden"
            :size="6"
            :default-query="function () { return { query: { terms: { 'id': ['{{ implode("','", $productIds) }}'] } } } }"
        >
            <strong class="font-bold text-2xl mt-5" slot="renderResultStats">
                @lang($title)
            </strong>

            <div slot="renderNoResults"></div>

            @include('rapidez::category.partials.listing.item', ['perLine' => 6])
        </reactive-list>
    </reactive-base>
@else
    <reactive-base v-cloak :app="config.es_prefix + '_products_' + config.store" :url="config.es_url">
        <reactive-list
            component-id="{{ $code }}"
            data-field="id"
            list-class="flex flex-wrap mt-5 -mx-1 overflow-hidden"
            :size="6"
            :default-query="function () { return { query: { terms: { 'id': crossSellProducts } } } }"
        >
            <strong class="font-bold text-2xl mt-5" slot="renderResultStats">
                @lang($title)
            </strong>

            <div slot="renderNoResults"></div>

            @include('rapidez::category.partials.listing.item', ['perLine' => 6])
        </reactive-list>
    </reactive-base>
@endif
