<ais-hits-per-page :items="listingSlotProps.hitsPerPage">
    <template v-slot="{ items, refine }">
        <x-rapidez::input.select class="w-20">
            <option
                v-for="item in items"
                v-bind:key="item.value"
                v-bind:value="item.value"
                v-bind:selected="item.isRefined"
                v-on:change="refine(item.value)"
            >
                @{{ item.label }}
            </option>
        </x-rapidez::input.select>
    </template>
</ais-hits-per-page>
