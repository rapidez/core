<lazy v-slot="{ intersected }">
    <listing
        v-if="intersected"
        v-bind:base-filters="() => [{
            function_score: {
                field_value_factor: {
                    field: 'reviews_count',
                }
            },
        }]"
        v-slot="{ loaded, index, searchClient, middlewares }"
        v-cloak
    >
        <div>
            <ais-instant-search
                v-if="searchClient"
                :search-client="searchClient"
                :index-name="index"
                :middlewares="middlewares"
            >
                <h2 class="font-medium text-2xl mb-5">
                    @lang('Popular products')
                </h2>
                <ais-configure :hits-per-page.camel="6" :page="0"/>
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
