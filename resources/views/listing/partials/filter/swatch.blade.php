<ais-refinement-list
    v-if="filter.text_swatch || filter.visual_swatch"
    :limit="64"
    :operator="filter.input == 'multiselect' ? 'and' : 'or'"
    :attribute="filter.code"
>
    <template v-slot="{ items, refine }">
        <div v-show="items.length" class="relative pb-5">
            <x-rapidez::filter.heading>
                <ul class="flex flex-wrap gap-x-1.5 gap-y-2 items-center pr-14 pb-1">
                    <li v-for="item in withSwatches(items, filter)">
                        <label
                            v-if="filter.visual_swatch"
                            class="block size-6 cursor-pointer flex border items-center justify-center hover:opacity-75 has-[:focus]:opacity-75 rounded-full border-black/15"
                            v-bind:class="{ 'ring-black ring-1 ring-offset-2': item.isRefined }"
                            v-bind:style="{ background: item.swatch?.swatch ?? 'none' }"
                        >
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
            </x-rapidez::filter.heading>
        </div>
    </template>
</ais-refinement-list>
