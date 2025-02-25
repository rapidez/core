{{-- TODO: Can/should we hide this filter when there are no results? --}}
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
        <span :hidden="!isSearchStalled">Loading...</span>
        --}}
    </template>
</ais-search-box>
