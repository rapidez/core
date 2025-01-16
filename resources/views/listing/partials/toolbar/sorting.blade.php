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
        <x-rapidez::input.select>
            <option
                v-for="item in items"
                v-bind:key="item.value"
                v-bind:value="item.value"
                v-bind:selected="item.value === currentRefinement"
                v-on:change="refine(item.value)"
            >
                @{{ item.label }}
            </option>
        </x-rapidez::input.select>
    </template>
</ais-sort-by>
