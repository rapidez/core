<ais-sort-by :items="sortOptions">
    <template v-slot="{ items, currentRefinement, refine }">
        <x-rapidez::input.select
            {{--
                TODO: The routing value does not seem to get propagated properly and gets reset
                Below is a gross fix that should not be here, but I could not find anything nicer that worked.
            --}}
            v-bind:set="(() => {
                if (listingSlotProps.sortBy) {
                    refine(listingSlotProps.sortBy);
                    listingSlotProps.sortBy = null;
                }
            })()"
            v-bind:value="items.find(item => item.value === currentRefinement)?.value"
            v-on:change="event => refine(event.target.value)"
        >
            <option
                v-for="item in items"
                v-bind:key="item.value"
                v-bind:value="item.value"
            >
                @{{ item.label }}
            </option>
        </x-rapidez::input.select>
    </template>
</ais-sort-by>
