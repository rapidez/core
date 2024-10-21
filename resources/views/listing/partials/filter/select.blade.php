<multi-list
    v-else
    :component-id="filter.code"
    :data-field="filter.code+'.keyword'"
    :inner-class="{
        count: 'text-inactive',
        list: '!max-h-full [&>li]:!h-auto',
        label: 'text-inactive before:shrink-0'
    }"
    :react="{and: filter.input == 'multiselect' ? listingScope.reactiveFilters : listingScope.reactiveFilters.filter(item => item != filter.code) }"
    :query-format="filter.input == 'multiselect' ? 'and' : 'or'"
    :show-search="false"
    u-r-l-params
>
    <div slot="render" class="relative pb-4" slot-scope="selectFilterScope" v-if="selectFilterScope.data.length > 1">
        <x-rapidez::filter.heading>
            <toggler>
                <ul class="flex flex-col gap-1" slot-scope="selectFilterTogglerScope">
                    <li
                        v-for="item, index in selectFilterScope.data"
                        v-if="index < 6 || selectFilterTogglerScope.isOpen"
                        :key="item._id"
                        class="flex justify-between text-base text-inactive"
                    >
                        <div class="flex">
                            <x-rapidez::checkbox
                                v-bind:checked="selectFilterScope.value[item.key]"
                                v-on:change="selectFilterScope.handleChange(item.key)"
                            >
                                <div class="font-sans font-medium text-inactive items-center text-sm flex" :class="{'text-neutral': selectFilterScope.value[item.key] == true}">
                                    @{{ item.key }}
                                    <span class="block ml-0.5 text-xs">(@{{ item.doc_count }})</span>
                                </div>
                            </x-rapidez::checkbox>
                        </div>
                    </li>
                    <li v-if="selectFilterScope.data.length > 6">
                        <button class="text-sm text-primary" @click="selectFilterTogglerScope.toggle">
                            <span v-if="selectFilterTogglerScope.isOpen" class="flex gap-x-4">
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
