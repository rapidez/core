<multi-list
    v-else
    :component-id="filter.code"
    :data-field="filter.code+'.keyword'"
    :inner-class="{
        count: 'text-inactive',
        list: '!max-h-full [&>li]:!h-auto',
        label: 'text-inactive before:shrink-0'
    }"
    :react="{and: filter.input == 'multiselect' ? reactiveFilters : reactiveFilters.filter(item => item != filter.code) }"
    :query-format="filter.input == 'multiselect' ? 'and' : 'or'"
    :show-search="false"
    u-r-l-params
>
    <div slot="render" class="relative pb-4" slot-scope="{ data, handleChange, value }" v-if="data.length > 1">
        <x-rapidez::filter.heading>
            <toggler>
                <ul class="flex flex-col gap-1" slot-scope="{ toggle, isOpen }">
                    <li
                        v-for="item, index in data"
                        v-if="index < 6 || isOpen"
                        :key="item._id"
                        class="flex justify-between text-base text-inactive"
                    >
                        <x-rapidez::input.checkbox
                            v-bind:checked="value[item.key]"
                            v-on:change="handleChange(item.key)"
                        >
                            <span class="font-sans font-medium text-sm items-center flex text-inactive" :class="{'text-neutral': value[item.key] == true}">
                                @{{ item.key }}
                                <span class="flex items-center ml-0.5 text-xs">(@{{ item.doc_count }})</span>
                            </span>
                        </x-rapidez::input.checkbox>
                    </li>
                    <li v-if="data.length > 6">
                        <button class="text-sm text-primary" @click="toggle">
                            <span v-if="isOpen" class="flex gap-x-4">
                                @lang('Less options')
                            </span>
                            <span v-else class="flex gap-x-4">
                                @lang('More options')
                            </span>
                        </button>
                    </li>
                </ul>
            </toggler>
        </x-rapidez::filter.heading>
    </div>
</multi-list>
