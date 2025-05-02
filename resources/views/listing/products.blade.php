{{-- TODO: Is this wrapper with these classes still the cleanest option? --}}
@pushOnce('head', 'listing-products')
    @vite(
        vite_filename_paths([
            'HitsPerPage.vue',
            'SortBy.vue',
            'Stats.vue',
            'Hits.vue',
            'Highlight.vue',
            'Pagination.vue',
            'Hits'
        ])
    )
@endPushOnce

<div id="products" class="flex flex-col gap-3 max-lg:mt-3 max-lg:flex-wrap">
    <x-rapidez::button.secondary for="category-filters-slideover" class="w-full lg:hidden">
        @lang('Filters')
    </x-rapidez::button.secondary>
    <div class="*:flex-wrap *:gap-3 *:max-sm:gap-y-3 *:max-md:justify-end">
        @include('rapidez::listing.partials.toolbar')
    </div>
    <ais-hits>
        <template v-slot="{ items }">
            <div v-if="items.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2 md:gap-5 overflow-hidden">
                <template v-for="(item, count) in items">
                    @include('rapidez::listing.partials.item')
                </template>
            </div>
            <div v-else>
                @include('rapidez::listing.partials.no-results')
            </div>
        </template>
    </ais-hits>

    @include('rapidez::listing.partials.pagination')
</div>
