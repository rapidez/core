<multi-list
    v-else-if="filter.input == 'boolean'"
    :component-id="filter.code"
    :data-field="filter.code+(filter.type != 'int' ? '.keyword' : '')"
    :react="{and: reactiveFilters}"
    :show-search="false"
    u-r-l-params
>
    <div
        slot="render"
        class="relative pb-4"
        slot-scope="{ data, handleChange, value }"
        v-if="data.length > 0"
    >
        <x-rapidez::filter.heading>
            <ul class="flex flex-col gap-1">
                <li class="flex" v-for="item in data">
                    <x-rapidez::input.checkbox
                        v-bind:checked="value[item.key]"
                        v-on:change="handleChange(item.key)"
                    >
                        <div
                            class="font-sans font-medium text-sm items-center flex"
                            :class="value[item.key] ? 'text' : 'text-muted'"
                        >
                            <template v-if="item.key">@lang('Yes')</template>
                            <template v-if="!item.key">@lang('No')</template>
                            <span class="block ml-0.5 text-xs">(@{{ item.doc_count }})</span>
                    </div>
                    </x-rapidez::input.checkbox>
                </li>
            </ul>
        </x-rapidez::filter.heading>
    </div>
</multi-list>
