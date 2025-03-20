<ais-refinement-list
    v-if="['select', 'multiselect'].includes(filter.input) && !(filter.text_swatch || filter.visual_swatch)"
    :operator="filter.input == 'multiselect' ? 'and' : 'or'"
    :attribute="filter.code"
    show-more
>
    <template v-slot="{ items, refine, isShowingMore, toggleShowMore, canToggleShowMore }">
        {{--
        TODO: Why do we need this pb-4 in here?
        We also have padding in the heading?
        Maybe handle that in 1 place?
        --}}
        <div v-show="items.length" class="relative pb-4">
            <x-rapidez::filter.heading>
                <ul class="flex flex-col gap-1">
                    <li
                        v-for="item in items"
                        class="flex justify-between text-base text-muted"
                    >
                        <div class="flex">
                            <x-rapidez::input.checkbox
                                v-bind:checked="item.isRefined"
                                v-on:change="refine(item.value)"
                            >
                                <span
                                    class="font-sans font-medium items-center text-sm flex"
                                    :class="item.isRefined ? 'text' : 'text-muted'"
                                >
                                    @{{ item.label }}
                                    <span class="block ml-0.5 text-xs">(@{{ item.count }})</span>
                                </span>
                            </x-rapidez::input.checkbox>
                        </div>
                    </li>
                </ul>

                <button
                    v-if="canToggleShowMore"
                    v-on:click="toggleShowMore"
                    class="text-sm text-primary"
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
