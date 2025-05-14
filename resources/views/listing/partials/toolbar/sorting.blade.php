<ais-sort-by :items="Object.values(config.searchkit.sorting)">
    <template v-slot="{ items, currentRefinement, refine }">
        <select
            v-bind:value="items.find(item => item.value === currentRefinement)?.value"
            v-on:change="event => refine(event.target.value)"
            class="py-0 pr-5 border-none text text-sm focus:outline-none focus:ring-0 focus:ring-offset-0 custom-select"
        >
            <option
                v-for="item in items"
                v-bind:key="item.value"
                v-bind:value="item.value"
            >
                @{{ item.label }}
            </option>
        </select>
    </template>
</ais-sort-by>
