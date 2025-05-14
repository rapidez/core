<ais-search-box class="relative mb-5">
    <template v-slot="{ currentRefinement, isSearchStalled, refine }">
        <x-rapidez::input
            type="search"
            v-bind:value="currentRefinement"
            v-on:input="refine($event.currentTarget.value)"
            class="peer"
            :placeholder="__('Search within the results')"
        />
        <x-heroicon-o-magnifying-glass class="size-5 absolute top-1/2 -translate-y-1/2 right-4 transition-opacity opacity-0 peer-placeholder-shown:opacity-100" />
        <button
            v-on:click="refine('')"
            class="absolute top-1/2 -translate-y-1/2 right-4 transition-opacity opacity-100 peer-placeholder-shown:pointer-events-none peer-placeholder-shown:opacity-0"
            type="reset"
            title="__('Clear the search query')"
            v-cloak
        >
            <x-heroicon-s-x-mark class="size-5" />
        </button>
    </template>
</ais-search-box>
<ais-stats-analytics></ais-stats-analytics>
