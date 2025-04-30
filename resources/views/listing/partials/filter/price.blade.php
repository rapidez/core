<ais-range-input
    v-if="rangeAttributes.includes(filter.code)"
    :attribute="filter.code"
>
    <template v-slot="{ currentRefinement, range, canRefine, refine, sendEvent }">
        <div v-show="range.max">
            <x-rapidez::filter.heading>
                <div class="flex flex-col">
                    <div class="flex flex-1">
                        <x-rapidez::input.range-slider
                            v-bind:range="range"
                            v-bind:current="currentRefinement"
                            v-on:change="refine"
                        />
                    </div>
                </div>
            </x-rapidez::filter.heading>
        </div>
    </template>
</ais-range-input>
