<ais-refinement-list
    v-if="filter.text_swatch || filter.visual_swatch"
    :operator="filter.input == 'multiselect' ? 'and' : 'or'"
    :attribute="filter.code"
>
    <template v-slot="{ items, refine, isShowingMore, toggleShowMore, canToggleShowMore }">
        <x-rapidez::accordion.filter v-show="items.length" canToggleShowMore>
            <x-slot:content>
                <ul class="flex flex-wrap gap-x-1.5 gap-y-2 items-center pr-14">
                    <li v-for="item in withSwatches(items, filter)">
                        <template v-if="filter.visual_swatch">
                            <x-rapidez::input.swatch.visual
                                color="item.swatch?.swatch ?? 'none'"
                                type="checkbox"
                                v-on:change="refine(item.value)"
                                v-bind:checked="item.isRefined"
                                v-bind:name="filter.code"
                                v-bind:aria-label="item.swatch?.label ?? item.value"
                                v-bind:id="item.swatch?.label ?? item.value"
                            >
                                @{{ item.swatch?.label ?? item.value }}
                            </x-rapidez::input.swatch.visual>
                        </template>
                        <template v-else>
                            <x-rapidez::input.swatch.text
                                type="checkbox"
                                v-on:change="refine(item.value)"
                                v-bind:checked="item.isRefined"
                                v-bind:aria-label="item.swatch?.label ?? item.value"
                                v-bind:id="item.swatch?.label ?? item.value"
                            >
                                @{{ item.swatch?.label ?? item.value }}
                            </x-rapidez::input.swatch.text>
                        </template>
                    </li>
                </ul>
            </x-slot:content>
        </x-rapidez::accordion.filter>
    </template>
</ais-refinement-list>
