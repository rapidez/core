<ais-range-input
    v-if="filter.input == 'price'"
    :attribute="filter.code"
>
    <template v-slot="{ currentRefinement, range, canRefine, refine, sendEvent }">
        <x-rapidez::filter.heading>
            <div class="flex flex-col">
                <div class="flex flex-1">
                    <x-rapidez::input.range-slider
                        v-bind:range="range"
                        v-bind:current="currentRefinement"
                        v-on:change="refine"
                    />
                </div>
                <div class="flex">
                    <input
                        class="w-1/2"
                        type="number"
                        :min="range.min"
                        :max="range.max"
                        :placeholder="range.min"
                        :disabled="!canRefine"
                        :value="currentRefinement.min ?? range.min"
                        v-on:input="refine({ min: $event.currentTarget.value, max: currentRefinement.max })"
                    />
                    <input
                        class="w-1/2"
                        type="number"
                        :min="range.min"
                        :max="range.max"
                        :placeholder="range.max"
                        :disabled="!canRefine"
                        :value="currentRefinement.max ?? range.max"
                        v-on:input="refine({ min: currentRefinement.min, max: $event.currentTarget.value })"
                    />
                </div>
            </div>
        </x-rapidez::filter.heading>
    </template>
</ais-range-input>
