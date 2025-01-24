<ais-refinement-list
    v-if="filter.text_swatch || filter.visual_swatch"
    :limit="64"
    :operator="filter.input == 'multiselect' ? 'and' : 'or'"
    :attribute="filter.code"
>
    <template v-slot="{ items, refine }">
        <div v-show="items.length" class="relative pb-4">
            <x-rapidez::filter.heading>
                <ul class="flex flex-wrap gap-2 items-center my-1">
                    <li v-for="item in withSwatches(items, filter)">
                        <label
                            v-if="filter.visual_swatch"
                            class="size-6 ring-1 ring-emphasis/10 ring-inset cursor-pointer flex items-center justify-center hover:opacity-75 rounded-full transition"
                            v-bind:class="{ 'outline outline-2 outline-emphasis outline-offset-1': item.isRefined }"
                            v-bind:style="{ background: item.swatch.swatch }"
                        >
                            <input
                                type="checkbox"
                                v-bind:checked="item.isRefined"
                                v-on:change="refine(item.value)"
                                class="hidden"
                            >
                        </label>
                        <label
                            v-else
                            class="border px-3 transition-all rounded cursor-pointer text-sm text-muted font-medium"
                            v-bind:class="{ 'outline outline-2 outline-emphasis outline-offset-1': item.isRefined }"
                        >
                            @{{ item.swatch.swatch }}
                            <input
                                type="checkbox"
                                v-bind:checked="item.isRefined"
                                v-on:change="refine(item.value)"
                                class="hidden"
                            >
                        </label>
                    </li>
                </ul>
            </x-rapidez::filter.heading>
        </div>
    </template>
</ais-refinement-list>
