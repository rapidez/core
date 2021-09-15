<multi-list
    v-else
    :component-id="filter.code"
    :data-field="filter.code+'.keyword'"
    :inner-class="{
        title: 'capitalize font-semibold',
        count: 'text-gray-400',
        list: '!max-h-full',
        label: 'text-gray-600'
    }"
    :title="filter.name.replace('_', ' ')"
    :react="{and: filter.input == 'multiselect' ? reactiveFilters : reactiveFilters.filter(item => item != filter.code) }"
    :query-format="filter.input == 'multiselect' ? 'and' : 'or'"
    :show-search="false"
    u-r-l-params
></multi-list>
