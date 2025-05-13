<ais-range-input
    v-if="rangeAttributes.includes(filter.code)"
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
                        >
                            <x-slot:value>
                                @{{ window.price(value, { maximumFractionDigits: 0 }) }}
                            </x-slot:value>
                        </x-rapidez::input.range-slider>
                    </div>

                    <div class="flex gap-10">
                        <div class="relative w-full">
                            <x-rapidez::input
                                class="text-right"
                                required
                                type="number"
                                v-bind:min="range.min"
                                v-bind:max="range.max"
                                v-bind:placeholder="range.min"
                                v-bind:disabled="!canRefine"
                                v-bind:value="currentRefinement.min ?? range.min"
                                v-on:input="if (!$event.currentTarget.validationMessage) {
                                    refine({ min: Math.max($event.currentTarget.value, range.min), max: currentRefinement.max })
                                }"
                            />
                            <span
                                class="absolute bottom-1/2 translate-y-1/2"
                                v-bind:class="{
                                    'left-2': $root.currencyDisplay.symbolLocation === 'left',
                                    'right-2': $root.currencyDisplay.symbolLocation === 'right',
                                }"
                            >
                                @{{ $root.currencyDisplay.symbol }}
                            </span>
                        </div>
                        <div class="relative w-full">
                            <x-rapidez::input
                                class="text-right"
                                required
                                type="number"
                                v-bind:min="range.min"
                                v-bind:max="range.max"
                                v-bind:placeholder="range.max"
                                v-bind:disabled="!canRefine"
                                v-bind:value="currentRefinement.max ?? range.max"
                                v-on:input="if (!$event.currentTarget.validationMessage) {
                                    refine({ min: currentRefinement.min, max: Math.min($event.currentTarget.value, range.max) })
                                }"
                            />
                            <span
                                class="absolute bottom-1/2 translate-y-1/2"
                                v-bind:class="{
                                    'left-2': $root.currencyDisplay.symbolLocation === 'left',
                                    'right-2': $root.currencyDisplay.symbolLocation === 'right',
                                }"
                            >
                                @{{ $root.currencyDisplay.symbol }}
                            </span>
                        </div>
                    </div>
                </div>
            </x-rapidez::filter.heading>
        </div>
    </template>
</ais-range-input>
