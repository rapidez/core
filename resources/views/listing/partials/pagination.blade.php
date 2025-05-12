<ais-pagination :padding="2" v-on:page-change="scrollToElement('#products', 50)">
    <template v-slot="{ currentRefinement, nbPages, pages, isFirstPage, isLastPage, refine, createURL }">
        <ul v-show="nbPages > 1" class="flex flex-wrap sm:justify-center w-full gap-y-2 max-sm:*:px-1 sm:gap-2">
            <li v-if="!isFirstPage" class="max-sm:w-1/2">
                <x-rapidez::button
                    ::href="createURL(0)"
                    v-on:click.exact.left.prevent="refine(0)"
                    :aria-label="__('First Page')"
                    class="max-w-12 max-sm:w-full max-sm:max-w-none"
                >
                    <x-heroicon-s-chevron-double-left class="shrink-0 size-4" />
                </x-rapidez::button>
            </li>
            <li v-if="!isFirstPage" class="max-sm:w-1/2">
                <x-rapidez::button
                    ::href="createURL(currentRefinement - 1)"
                    v-on:click.exact.left.prevent="refine(currentRefinement - 1)"
                    :aria-label="__('Previous Page')"
                    class="max-w-12 max-sm:w-full max-sm:max-w-none"
                >
                    <x-heroicon-s-chevron-left class="shrink-0 size-4" />
                </x-rapidez::button>
            </li>
            <li v-for="page in pages" :key="page" class="max-sm:flex-1">
                <x-rapidez::button
                    ::href="createURL(page)"
                    ::class="{ '!bg-white !border !border-default !border-b-default': page === currentRefinement }"
                    v-on:click.exact.left.prevent="refine(page)"
                    ::aria-label="`{{ __('Page') }} ${page + 1}`"
                    class="max-sm:w-full sm:max-w-12"
                >
                    @{{ page + 1 }}
                </x-rapidez::button>
            </li>
            <li v-if="!isLastPage" class="max-sm:w-1/2">
                <x-rapidez::button
                    ::href="createURL(currentRefinement + 1)"
                    v-on:click.exact.left.prevent="refine(currentRefinement + 1)"
                    :aria-label="__('Next Page')"
                    class="max-w-12 max-sm:w-full max-sm:max-w-none"
                >
                    <x-heroicon-s-chevron-right class="shrink-0 size-4" />
                </x-rapidez::button>
            </li>
            <li v-if="!isLastPage" class="max-sm:w-1/2">
                <x-rapidez::button
                    ::href="createURL(nbPages)"
                    v-on:click.exact.left.prevent="refine(nbPages)"
                    :aria-label="__('Last Page')"
                    class="max-w-12 max-sm:w-full max-sm:max-w-none"
                >
                    <x-heroicon-s-chevron-double-right class="shrink-0 size-4" />
                </x-rapidez::button>
            </li>
        </ul>
    </template>
</ais-pagination>
