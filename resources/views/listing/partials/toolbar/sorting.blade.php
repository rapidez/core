{{--
TODO: The "sortOptions" computed prop should be used,
but the value is the index instead of an attribute.
--}}
<ais-sort-by :items="[
    { value: 'instant_search', label: 'Featured' },
    { value: 'instant_search_price_asc', label: 'Price asc' },
    { value: 'instant_search_price_desc', label: 'Price desc' },
]">
    <template v-slot="{ items, currentRefinement, refine }">
        <x-rapidez::input.select
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
