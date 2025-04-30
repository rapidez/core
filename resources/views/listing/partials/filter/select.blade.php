<ais-refinement-list
    v-if="['select', 'multiselect'].includes(filter.input) && !(filter.text_swatch || filter.visual_swatch)"
    :operator="filter.input == 'multiselect' ? 'and' : 'or'"
    :attribute="filter.code"
    :limit="6"
    show-more
>
    <template v-slot="{ items, refine, isShowingMore, toggleShowMore, canToggleShowMore }">
        {{--
        TODO: Why do we need this pb-4 in here?
        We also have padding in the heading?
        Maybe handle that in 1 place?
        --}}
        <div v-show="items.length" class="relative pb-5">
            <x-rapidez::filter.heading>
                <div class="flex flex-col *:py-1 first:*:pt-0 last:*:pb-0">
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

                <button
                    v-if="canToggleShowMore"
                    v-on:click="toggleShowMore"
                    class="text-sm text-primary font-medium mt-3 hover:underline"
                >
                    <span v-if="isShowingMore" class="flex gap-x-4">
                        @lang('Less options')
                    </span>
                    <span v-else class="flex gap-x-4">
                        @lang('More options')
                    </span>
                </button>
            </x-rapidez::filter.heading>
        </div>
    </template>
</ais-refinement-list>
