<ais-range-input
    v-if="listingSlotProps.rangeAttributes.includes(filter.code)"
    :attribute="filter.code"
>
    <template v-slot="{ currentRefinement, range, canRefine, refine, sendEvent }">
        <div v-show="range.max">
            <x-rapidez::filter.heading>
                {{-- TODO: Handle the padding globally within the component? --}}
                <div class="flex flex-col pb-4">
                    <div class="flex flex-1">
                        <x-rapidez::input.range-slider
                            v-bind:range="range"
                            v-bind:current="currentRefinement"
                            v-on:change="refine"
                        />
                    </div>

                    <div class="flex gap-10">
                        <x-rapidez::input
                            type="number"
                            v-bind:min="range.min"
                            v-bind:max="range.max"
                            v-bind:placeholder="range.min"
                            v-bind:disabled="!canRefine"
                            v-bind:value="currentRefinement.min ?? range.min"
                            v-on:input="refine({ min: $event.currentTarget.value, max: currentRefinement.max })"
                        />
                        <x-rapidez::input
                            class="text-right"
                            type="number"
                            v-bind:min="range.min"
                            v-bind:max="range.max"
                            v-bind:placeholder="range.max"
                            v-bind:disabled="!canRefine"
                            v-bind:value="currentRefinement.max ?? range.max"
                            v-on:input="refine({ min: currentRefinement.min, max: $event.currentTarget.value })"
                        />
                    </div>
                </div>
            </x-rapidez::filter.heading>
        </div>
    </template>
</ais-range-input>
