<ais-refinement-list
    v-if="['select', 'multiselect'].includes(filter.input) && !(filter.text_swatch || filter.visual_swatch)"
    :operator="filter.input == 'multiselect' ? 'and' : 'or'"
    :attribute="filter.code"
    :limit="6"
    show-more
    class="relative -mx-1 -mt-1"
>
    <template v-slot="{ items, refine, isShowingMore, toggleShowMore, canToggleShowMore }">
        <div class="overflow-clip">
            <x-rapidez::accordion.filter v-show="items.length" class="details-content:overflow-visible px-1 py-1" canToggleShowMore>
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
                                    <span v-html="stripHtmlTags(item.label)"></span>
                                    <span class="block ml-0.5 text-xs" data-testid="listing-filter-count">
                                        (@{{ item.count }})
                                    </span>
                                </span>
                            </x-rapidez::input.checkbox>
                        </template>
                    </div>
                </x-slot:content>
            </x-rapidez::accordion.filter>
        </div>
    </template>
</ais-refinement-list>
