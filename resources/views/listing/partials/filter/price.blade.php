<ais-range-input
    v-if="filter.input == 'price'"
    :attribute="filter.code"
>
    <template v-slot="{ currentRefinement, range, canRefine, refine, sendEvent }">
        <x-rapidez::filter.heading>
            {{-- TODO: This should become a slider --}}
            <div class="flex">
                <input
                    class="w-1/2"
                    type="number"
                    :min="range.min"
                    :max="range.max"
                    :placeholder="range.min"
                    :disabled="!canRefine"
                    :value="currentRefinement.min"
                    v-on:input="refine({
                      min: $event.currentTarget.value,
                      max: currentRefinement.max,
                    })"
                />
                <input
                    class="w-1/2"
                    type="number"
                    :min="range.min"
                    :max="range.max"
                    :placeholder="range.max"
                    :disabled="!canRefine"
                    :value="currentRefinement.max"
                    v-on:input="refine({
                      min: currentRefinement.min,
                      max: $event.currentTarget.value,
                    })"
                />
            </div>
        </x-rapidez::filter.heading>
    </template>
</ais-range-input>
