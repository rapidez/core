<ais-sort-by :items="Object.values(config.searchkit.sorting)">
    <template v-slot="{ items, currentRefinement, refine }">
        <x-rapidez::input.select
            v-bind:value="items.find(item => item.value === currentRefinement)?.value"
            v-on:change="event => refine(event.target.value)"
            class="pr-8"
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
