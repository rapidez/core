<ais-range-input
    v-if="rangeAttributes.includes(filter.code)"
    :attribute="filter.code"
>
    <template v-slot="{ currentRefinement, range, canRefine, refine, sendEvent }">
        <div v-show="range.max">
            <x-rapidez::accordion.filter>
                <x-slot:content>
                    <div class="flex flex-col">
                        <div class="flex flex-1">
                            <x-rapidez::input.range-slider
                                v-bind:range="range"
                                v-bind:current="currentRefinement"
                                v-on:change="refine"
                                v-bind:prefix="filter.code === 'price' && $root.currencySymbolLocation === 'left' ? $root.currencySymbol : ''"
                                v-bind:suffix="filter.code === 'price' && $root.currencySymbolLocation !== 'left' ? $root.currencySymbol : ''"
                            >
                                <x-slot:value>
                                    @{{ window.price(value, { maximumFractionDigits: 0 }) }}
                                </x-slot:value>
                            </x-rapidez::input.range-slider>
                        </div>
                    </div>
                </x-slot:content>
            </x-rapidez::accordion.filter>
        </div>
    </template>
</ais-range-input>
