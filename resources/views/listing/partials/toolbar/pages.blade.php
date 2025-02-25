<ais-hits-per-page :items="listingSlotProps.hitsPerPage">
    <template v-slot="{ items, refine }">
        <x-rapidez::input.select
            v-bind:value="items.find(item => item.isRefined)?.value"
            v-on:change="event => refine(event.target.value)"
            class="w-20"
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
</ais-hits-per-page>
