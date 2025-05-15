<ais-range-input
    v-if="rangeAttributes.includes(filter.code)"
    :attribute="filter.code"
    class="pb-5"
>
    <template v-slot="{ currentRefinement, range, canRefine, refine, sendEvent }">
        <div v-show="range.max">
            <x-rapidez::details.filter>
                <x-slot:content>
                    <div class="flex flex-col">
                        <div class="flex flex-1">
                            <x-rapidez::input.range-slider
                                v-bind:range="range"
                                v-bind:current="currentRefinement"
                                v-on:change="refine"
                                price
                            >
                                <x-slot:value>
                                    @{{ window.price(value, { maximumFractionDigits: 0 }) }}
                                </x-slot:value>
                            </x-rapidez::input.range-slider>
                        </div>
                    </div>
                </x-slot:content>
            </x-rapidez::details.filter>
        </div>
    </template>
</ais-range-input>
