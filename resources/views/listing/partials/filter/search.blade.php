<ais-search-box>
    <template v-slot="{ currentRefinement, isSearchStalled, refine }">
        <x-rapidez::input
            type="search"
            v-bind:value="currentRefinement"
            v-on:input="refine($event.currentTarget.value)"
            :placeholder="__('Search within the results')"
        />
        {{--
        TODO: Maybe use this for a loading icon with the input?
        But it's super duper fast, does it make sense?
        <span :hidden="!isSearchStalled">Loading...</span>
        --}}
    </template>
</ais-search-box>
<ais-stats-analytics></ais-stats-analytics>
