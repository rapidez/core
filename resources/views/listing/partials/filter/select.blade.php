<multi-list
    v-else
    :component-id="filter.code"
    :data-field="filter.code+'.keyword'"
    :inner-class="{
        title: 'capitalize font-semibold',
        count: 'text-gray-400',
        list: '!max-h-full [&>li]:!h-auto',
        label: 'text-gray-600 before:shrink-0'
    }"
    :title="filter.name.replace('_', ' ')"
    :react="{and: filter.input == 'multiselect' ? reactiveFilters : reactiveFilters.filter(item => item != filter.code) }"
    :query-format="filter.input == 'multiselect' ? 'and' : 'or'"
    :show-search="false"
    u-r-l-params
></multi-list>
