<ais-pagination class="flex">
    <template v-slot="{ currentRefinement, nbPages, pages, isFirstPage, isLastPage, refine, createURL }">
        <ul class="flex gap-2 mx-auto">
            <li v-if="!isFirstPage">
                <x-rapidez::button
                    ::href="createURL(0)"
                    v-on:click.prevent="refine(0)"
                >
                    @lang('First')
                </x-rapidez::button>
            </li>
            <li v-if="!isFirstPage">
                <x-rapidez::button
                    ::href="createURL(currentRefinement - 1)"
                    v-on:click.prevent="refine(currentRefinement - 1)"
                >
                    @lang('Prev')
                </x-rapidez::button>
            </li>
            <li v-for="page in pages" :key="page">
                <x-rapidez::button
                    ::href="createURL(page)"
                    ::class="{ '!font-bold': page === currentRefinement }"
                    v-on:click.prevent="refine(page)"
                >
                    @{{ page + 1 }}
                </x-rapidez::button>
            </li>
            <li v-if="!isLastPage">
                <x-rapidez::button
                    ::href="createURL(currentRefinement + 1)"
                    v-on:click.prevent="refine(currentRefinement + 1)"
                >
                    @lang('Next')
                </x-rapidez::button>
            </li>
            <li v-if="!isLastPage">
                <x-rapidez::button
                    ::href="createURL(nbPages)"
                    v-on:click.prevent="refine(nbPages)"
                >
                    @lang('Last')
                </x-rapidez::button>
            </li>
        </ul>
    </template>
</ais-pagination>
