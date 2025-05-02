<ais-pagination :padding="2" class="flex w-full" v-on:page-change="window.scrollTo({ top: 0, behavior: 'smooth' })">
    <template v-slot="{ currentRefinement, nbPages, pages, isFirstPage, isLastPage, refine, createURL }">
        <ul v-show="nbPages > 1" class="flex flex-wrap gap-2 w-full">
            <li v-for="page in pages" :key="page">
                <x-rapidez::button
                    ::href="createURL(page)"
                    ::class="{ '!bg-white !border !border-severe-emphasis !border-b-severe-emphasis': page === currentRefinement }"
                    v-on:click.exact.left.prevent="refine(page)"
                    ::aria-label="`{{ __('Page') }} ${page + 1}`"
                >
                    @{{ page + 1 }}
                </x-rapidez::button>
            </li>
            <li v-if="!isLastPage" class="max-sm:w-full sm:ml-auto">
                <x-rapidez::button
                    ::href="createURL(currentRefinement + 1)"
                    v-on:click.exact.left.prevent="refine(currentRefinement + 1)"
                    :aria-label="__('Next Page')"
                    class="max-sm:w-full"
                >
                    @lang('Next')
                </x-rapidez::button>
            </li>
        </ul>
    </template>
</ais-pagination>
