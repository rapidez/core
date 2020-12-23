<dynamic-range-slider
    v-if="filter.input == 'price'"
    :component-id="filter.code"
    :data-field="filter.code"
    :title="filter.name.replace('_', ' ')"
    :show-filter="false"
    :inner-class="{
        title: 'capitalize font-semibold',
    }"
    u-r-l-params
></dynamic-range-slider>
