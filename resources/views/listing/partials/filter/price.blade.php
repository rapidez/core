<dynamic-range-slider
    v-if="filter.input == 'price'"
    :component-id="filter.code"
    :data-field="filter.code"
    :title="filter.name.replace('_', ' ')"
    :react="{and: ['query-filter']}"
    :show-filter="false"
    :inner-class="{
        title: 'capitalize text-sm font-medium text-neutral',
    }"
    :slider-options="{ dragOnClick: true, useKeyboard: false }"
    u-r-l-params
></dynamic-range-slider>
