<multi-list
    v-else-if="filter.input == 'boolean'"
    :component-id="filter.code"
    :data-field="filter.code+(filter.type != 'int' ? '.keyword' : '')"
    :inner-class="{
        title: 'capitalize text-sm font-medium text-neutral',
        count: 'text-gray-400',
        list: '!max-h-full',
        label: 'ml-1 text-sm text-gray-600'
    }"
    :title="filter.name.replace('_', ' ')"
    :react="{and: reactiveFilters}"
    :show-search="false"
    u-r-l-params
>
    <span
        slot="renderItem"
        slot-scope="{ label, count }"
    >
        <template v-if="label">@lang('Yes')</template>
        <template v-else>@lang('No')</template>
        <span class="text-gray-600">(@{{ count }})</span>
    </span>
</multi-list>
