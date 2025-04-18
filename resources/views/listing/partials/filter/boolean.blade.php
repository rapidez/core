<ais-refinement-list
    v-if="filter.input == 'boolean'"
    :attribute="filter.code"
    operator="and"
>
    <template v-slot="{ items, refine }">
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
                                    <template v-if="item.value == '1'">
                                        @lang('Yes')
                                    </template>
                                    <template v-if="item.value == '0'">
                                        @lang('No')
                                    </template>
                                    <span class="block ml-0.5 text-xs">
                                        (@{{ item.count }})
                                    </span>
                                </span>
                            </x-rapidez::input.checkbox>
                        </div>
                    </li>
                </ul>
            </x-rapidez::filter.heading>
        </div>
    </template>
</ais-refinement-list>

