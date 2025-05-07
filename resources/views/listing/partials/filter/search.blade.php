<ais-search-box class="mb-5">
    <template v-slot="{ currentRefinement, isSearchStalled, refine }">
        <x-rapidez::input
            type="search"
            v-bind:value="currentRefinement"
            v-on:input="refine($event.currentTarget.value)"
            :placeholder="__('Search within the results')"
        />
    </template>
</ais-search-box>
<ais-stats-analytics></ais-stats-analytics>
