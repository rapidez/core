<ais-refinement-list
    v-if="filter.text_swatch || filter.visual_swatch"
    :operator="filter.input == 'multiselect' ? 'and' : 'or'"
    :attribute="filter.code"
>
    <template v-slot="{ items, refine, isShowingMore, toggleShowMore, canToggleShowMore }">
        <x-rapidez::details.filter v-show="items.length" :canToggleShowMore="true">
            <x-slot:content>
                <ul class="flex flex-wrap gap-x-1.5 gap-y-2 items-center pr-14">
                    <li v-for="item in withSwatches(items, filter)">
                        <label
                            v-if="filter.visual_swatch"
                            class="block cursor-pointer flex items-center justify-center p-1 rounded-full ring-inset ring-1 has-[:focus]:ring-emphasis"
                            v-bind:class="{
                                'ring-default ring-1 hover:ring-emphasis': !item.isRefined,
                                'ring-active ring-2': item.isRefined
                            }"
                        >
                            <span class="size-6 block border border-black/15 rounded-full m-px" v-bind:style="{ background: item.swatch?.swatch ?? 'none' }"></span>
                            <input
                                type="checkbox"
                                v-bind:checked="item.isRefined"
                                v-on:change="refine(item.value)"
                                class="opacity-0 size-0"
                            >
                        </label>

                        <label
                            v-else
                            class="block border px-3 py-1.5 rounded-md cursor-pointer text-sm text-muted font-medium hover:border-emphasis has-[:focus]:border-emphasis"
                            v-bind:class="{ 'bg-active !border-active text-white': item.isRefined }"
                            v-bind:style="{ background: item.swatch?.swatch ?? 'none' }"
                        >
                            @{{ item.swatch?.swatch ?? item.value }}
                            <input
                                type="checkbox"
                                v-bind:checked="item.isRefined"
                                v-on:change="refine(item.value)"
                                class="opacity-0 size-0"
                            >
                        </label>
                    </li>
                </ul>
            </x-slot:content>
        </x-rapidez::details.filter>
    </template>
</ais-refinement-list>
