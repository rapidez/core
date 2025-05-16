<ais-pagination :padding="1" v-on:page-change="scrollToElement('main')">
    <template v-slot="{ currentRefinement, nbPages, pages, isFirstPage, isLastPage, refine, createURL }">
        <ul v-show="nbPages > 1" class="flex flex-wrap w-full pb-0.5 mt-5 gap-1 sm:gap-2 justify-center">
            <li v-if="!isFirstPage">
                <button
                    href="createURL(currentRefinement - 1)"
                    v-on:click.exact.left.prevent="refine(currentRefinement - 1)"
                    aria-label="@lang('Previous Page')"
                    class="flex justify-center items-center rounded border hover:border-emphasis max-sm:size-9 sm:pl-2 sm:pr-3 sm:h-full"
                >
                    <x-heroicon-o-chevron-left class="shrink-0 stroke-2 size-4" />
                    <span class="max-sm:hidden">@lang('Prev')</span>
                </button>
            </li>
            <li v-if="!isFirstPage && currentRefinement !== 1">
                <button
                    href="createURL(0)"
                    v-on:click.exact.left.prevent="refine(0)"
                    aria-label="@lang('First Page')"
                    class="flex justify-center items-center size-9 rounded border hover:border-emphasis sm:size-10"
                >
                    1
                </button>
            </li>
            <li v-if="!isFirstPage && currentRefinement !== 1 && currentRefinement !== 2" class="flex items-center text-muted max-sm:text-xs">
                ...
            </li>
            <li v-for="page in pages" :key="page">
                <button
                    v-bind:href="createURL(page)"
                    v-bind:class="{ 'ring-1 bg-primary/10 ring-primary border-primary font-semibold hover:border-primary': page === currentRefinement }"
                    v-on:click.exact.left.prevent="refine(page)"
                    v-bind:aria-label="`{{ __('Page') }} ${page + 1}`"
                    class="size-9 sm:size-10 rounded border hover:border-emphasis"
                >
                    @{{ page + 1 }}
                </button>
            </li>
            <li v-if="!isLastPage && currentRefinement !== nbPages - 2 && currentRefinement !== nbPages - 3" class="flex items-center text-muted max-sm:text-xs">
                ...
            </li>
            <li v-if="!isLastPage && currentRefinement !== nbPages - 2">
                <button
                    v-bind:href="createURL(nbPages)"
                    v-on:click.exact.left.prevent="refine(nbPages)"
                    aria-label="@lang('Last Page')"
                    class="flex justify-center items-center size-9 sm:size-10 rounded border hover:border-emphasis"
                >
                    @{{ nbPages }}
                </button>
            </li>
            <li v-if="!isLastPage">
                <button
                    v-bind:href="createURL(currentRefinement + 1)"
                    v-on:click.exact.left.prevent="refine(currentRefinement + 1)"
                    aria-label="@lang('Next Page')"
                    class="flex justify-center items-center rounded border hover:border-emphasis max-sm:size-9 sm:pr-2 sm:pl-3 sm:h-full"
                >
                    <span class="max-sm:hidden">@lang('Next')</span>
                    <x-heroicon-o-chevron-right class="shrink-0 stroke-2 size-4 mt-0.5" />
                </button>
            </li>
        </ul>
    </template>
</ais-pagination>
