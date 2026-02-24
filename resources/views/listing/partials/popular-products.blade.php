@if (Rapidez::config('reports/options/product_view_enabled'))
    <lazy v-slot="{ intersected }">
        <listing
            v-if="intersected"
            v-bind:query="() => [{
                function_score: {
                    field_value_factor: {
                        field: 'popularity',
                        missing: 0,
                    },
                },
            }]"
            v-slot="popularProducts"
            v-cloak
        >
            <div ref="root">
                <ais-instant-search
                    v-if="popularProducts.searchClient"
                    :future="{ preserveSharedStateOnUnmount: true }"
                    :search-client="popularProducts.searchClient"
                    :index-name="popularProducts.index"
                    :middlewares="popularProducts.middlewares"
                >
                    <h2 class="font-medium text-2xl mb-5">
                        @lang('Popular products')
                    </h2>
                    {{-- __NO_QUERY__ is a temporary band-aid for https://github.com/searchkit/searchkit/pull/1407 --}}
                    <ais-configure :hits-per-page.camel="6" :page="0" query="__NO_QUERY__"/>
                    <ais-hits v-slot="{ items, sendEvent }">
                        <div v-if="items && items.length" class="overflow-hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 -mx-5 -mb-px *:border-b">
                                <template v-for="(item, count) in items">
                                    @include('rapidez::listing.partials.item')
                                </template>
                            </div>
                        </div>
                    </ais-hits>
                </ais-instant-search>
            </div>
        </listing>
    </lazy>
@endif
