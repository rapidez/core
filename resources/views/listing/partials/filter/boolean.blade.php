<ais-refinement-list
    v-if="filter.input == 'boolean'"
    :attribute="filter.code"
>
    <template v-slot="{ items, refine }">
        <div v-if="items.length" class="relative pb-4">
            <x-rapidez::filter.heading>
                {{-- @{{items}} --}}
                <ul class="flex flex-col gap-1">
                    <li
                        v-for="item in items"
                        class="flex justify-between text-base text-muted"
                    >
                        <div class="flex">
                            {{-- TODO: checked state is acting weird --}}
                            {{-- @{{ item.isRefined }} --}}
                            <x-rapidez::input.checkbox
                                v-bind:checked="item.isRefined"
                                v-on:change="refine(item.value)"
                            >
                                <span
                                    class="font-sans font-medium items-center text-sm flex"
                                    :class="item.isRefined ? 'text' : 'text-muted'"
                                >
                                    <template v-if="item.value == 'true'">@lang('Yes')</template>
                                    <template v-if="item.value == 'false'">@lang('No')</template>
                                    <span class="block ml-0.5 text-xs">(@{{ item.count }})</span>
                                </span>
                            </x-rapidez::input.checkbox>
                        </div>
                    </li>
                </ul>
            </x-rapidez::filter.heading>
        </div>
    </template>
</ais-refinement-list>

