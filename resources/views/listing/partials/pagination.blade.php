<ais-pagination class="flex" v-on:page-change="scrollToElement('#products')">
    <template v-slot="{ currentRefinement, nbPages, pages, isFirstPage, isLastPage, refine, createURL }">
        <ul class="flex gap-2 mx-auto">
            <li v-if="!isFirstPage">
                <x-rapidez::button
                    ::href="createURL(0)"
                    v-on:click.exact.left.prevent="refine(0)"
                    :aria-label="__('First Page')"
                >
                    @lang('First')
                </x-rapidez::button>
            </li>
            <li v-if="!isFirstPage">
                <x-rapidez::button
                    ::href="createURL(currentRefinement - 1)"
                    v-on:click.exact.left.prevent="refine(currentRefinement - 1)"
                    :aria-label="__('Previous Page')"
                >
                    @lang('Prev')
                </x-rapidez::button>
            </li>
            <li v-for="page in pages" :key="page">
                <x-rapidez::button
                    ::href="createURL(page)"
                    ::class="{ '!font-bold': page === currentRefinement }"
                    v-on:click.exact.left.prevent="refine(page)"
                    ::aria-label="`{{ __('Page') }} ${page + 1}`"
                >
                    @{{ page + 1 }}
                </x-rapidez::button>
            </li>
            <li v-if="!isLastPage">
                <x-rapidez::button
                    ::href="createURL(currentRefinement + 1)"
                    v-on:click.exact.left.prevent="refine(currentRefinement + 1)"
                    :aria-label="__('Next Page')"
                >
                    @lang('Next')
                </x-rapidez::button>
            </li>
            <li v-if="!isLastPage">
                <x-rapidez::button
                    ::href="createURL(nbPages)"
                    v-on:click.exact.left.prevent="refine(nbPages)"
                    :aria-label="__('Last Page')"
                >
                    @lang('Last')
                </x-rapidez::button>
            </li>
        </ul>
    </template>
</ais-pagination>
