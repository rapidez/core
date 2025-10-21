<ais-hits v-bind:index-name="config.index.product" v-bind:index-id="'autocomplete_' + config.index.product">
    <template v-slot="{ items, sendEvent }">
        <div v-if="items && items.length">
            <div class="mb-5 flex items-center justify-between">
                <p class="font-medium text-2xl inline-block">
                    <ais-state-results v-slot="{ query }">
                        <template v-if="query && query !== '__NO_QUERY__'">
                            @lang('Search for'): @{{ query }}
                        </template>
                        <template v-else>
                            @lang('Search')
                        </template>
                    </ais-state-results>
                </p>

                @include('rapidez::layouts.partials.header.autocomplete.all-results')
            </div>

            <div class="overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 -mx-5 -mb-px *:border-b">
                    <template v-for="(item, count) in items">
                        @include('rapidez::listing.partials.item')
                    </template>
                </div>
            </div>
        </div>
    </template>
</ais-hits>