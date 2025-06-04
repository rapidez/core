@pushOnce('head', 'listing-products')
    @vite(
        vite_filename_paths([
            'HitsPerPage.vue',
            'SortBy.vue',
            'Stats.vue',
            'Hits.vue',
            'Highlight.vue',
            'Pagination.vue',
            'Hits.js'
        ])
    )
@endPushOnce

<div id="products" class="flex flex-col max-lg:flex-wrap">
    <div class="pb-4 border-b">
        @include('rapidez::listing.partials.toolbar')
    </div>
    <ais-hits>
        <template v-slot="{ items, sendEvent }">
            <div v-if="items && items.length" class="overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 -mx-5 -mb-px *:border-b">
                    <template v-for="(item, count) in items">
                        @include('rapidez::listing.partials.item')
                    </template>
                </div>
            </div>
            <div v-else>
                @include('rapidez::listing.partials.no-results')
            </div>
        </template>
    </ais-hits>

    @include('rapidez::listing.partials.pagination')
</div>
