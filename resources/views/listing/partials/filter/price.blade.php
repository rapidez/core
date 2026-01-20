<ais-range-input
    v-if="listingSlotProps.rangeAttributes.includes(filter.code)"
    :attribute="filter.code == 'price' ? 'prices.'+(user.value?.group_id || 0)+'.min_price' : filter.code"
    class="relative -mx-1 -mt-1"
>
    <template v-slot="{ currentRefinement, range, canRefine, refine, sendEvent }">
        <div class="overflow-clip" v-show="range.max">
            <x-rapidez::accordion.filter class="details-content:overflow-visible px-1 py-1">
                <x-slot:content>
                    <div class="flex flex-col">
                        <div class="flex flex-1">
                            <x-rapidez::input.range-slider
                                v-bind:range="range"
                                v-bind:current="currentRefinement"
                                v-on:change="refine"
                                v-bind:prefix="filter.code === 'price' && $root.currencySymbolLocation.value === 'left' ? $root.currencySymbol : ''"
                                v-bind:suffix="filter.code === 'price' && $root.currencySymbolLocation.value === 'right' ? $root.currencySymbol : ''"
                            >
                                <x-slot:value>
                                    @{{ price(value, { maximumFractionDigits: 0 }) }}
                                </x-slot:value>
                            </x-rapidez::input.range-slider>
                        </div>
                    </div>
                </x-slot:content>
            </x-rapidez::accordion.filter>
        </div>
    </template>
</ais-range-input>
