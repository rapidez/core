<ais-refinement-list
    v-if="['select', 'multiselect'].includes(filter.input) && !(filter.text_swatch || filter.visual_swatch)"
    :operator="filter.input == 'multiselect' ? 'and' : 'or'"
    :attribute="filter.code"
    :limit="6"
    show-more
>
    <template v-slot="{ items, refine, isShowingMore, toggleShowMore, canToggleShowMore }">
        <x-rapidez::details.filter v-show="items.length" canToggleShowMore>
            <x-slot:content>
                <div class="flex flex-col *:py-1 first:*:pt-0 last:*:pb-0 items-start">
                    <template v-for="item in items">
                        <x-rapidez::input.checkbox
                            v-bind:checked="item.isRefined"
                            v-on:change="refine(item.value)"
                        >
                            <span
                                class="items-baseline flex text-base/5"
                                :class="item.isRefined ? 'text' : 'text-muted hover:text'"
                            >
                                @{{ item.label }}
                                <span class="block ml-0.5 text-xs">(@{{ item.count }})</span>
                            </span>
                        </x-rapidez::input.checkbox>
                    </template>
                </div>
            </x-slot:content>
        </x-rapidez::details.filter>
    </template>
</ais-refinement-list>
