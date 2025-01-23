<ais-range-input
    v-if="filter.input == 'price'"
    :attribute="filter.code"
>
    <template v-slot="{ currentRefinement, range, canRefine, refine, sendEvent }">
        <x-rapidez::filter.heading>
            <x-rapidez::input.range-slider
                v-bind:range="range"
                v-bind:current="currentRefinement"
                v-on:change="refine"
            />
        </x-rapidez::filter.heading>
    </template>
</ais-range-input>
