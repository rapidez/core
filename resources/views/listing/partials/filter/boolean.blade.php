<ais-refinement-list
    v-if="filter.input == 'boolean'"
    :attribute="filter.code"
    operator="and"
>
    <template v-slot="{ items, refine }">
        <x-rapidez::accordion.filter v-show="items.length">
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
                    </template>
                </div>
            </x-slot:content>
        </x-rapidez::details>
    </template>
</ais-refinement-list>

