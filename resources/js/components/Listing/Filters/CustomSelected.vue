<script>
import { swatches } from '../../../stores/useSwatches'

export default {
    props: {
        selectedValues: {
            type: Object,
            default: () => {},
        },
    },
    render() {
        return this.$scopedSlots.default(this)
    },

    methods: {
        getFilteredValues(values = {}) {
            const filteredValues = {}

            Object.keys(values).forEach((componentId) => {
                if (
                    values[componentId].showFilter &&
                    (Array.isArray(values[componentId].value) ? values[componentId].value.length : !!values[componentId].value)
                ) {
                    filteredValues[componentId] = values[componentId]
                }
            })

            return filteredValues
        },
    },

    computed: {
        activeFilters() {
            return Object.keys(this.selectedValues)
                .filter((filterKey) => {
                    return (
                        this.selectedValues[filterKey].showFilter &&
                        (Array.isArray(this.selectedValues[filterKey].value)
                            ? this.selectedValues[filterKey].value.length
                            : !!this.selectedValues[filterKey].value)
                    )
                })
                .flatMap((filterKey) => {
                    let result

                    if (
                        Array.isArray(this.selectedValues[filterKey].value) &&
                        !['category', 'price', window.config.color_attribute].includes(filterKey)
                    ) {
                        result = this.selectedValues[filterKey].value.map((selected) => selected)
                    }

                    if (filterKey === 'price') {
                        result = price(this.selectedValues[filterKey].value[0]) + ' - ' + price(this.selectedValues[filterKey].value[1])
                    }

                    if (filterKey === window.config.color_attribute) {
                        result = this.selectedValues[filterKey].value.map((selected) => {
                            return swatches.value[filterKey].options[selected].label
                        })
                    }
                    if (filterKey === 'category') {
                        result = config.subcategories[this.selectedValues[filterKey].value]
                    }

                    return result
                        ? Array.isArray(result)
                            ? result.map((value) => ({ code: filterKey, value: value }))
                            : [{ code: filterKey, value: result }]
                        : []
                })
        },
    },
}
</script>
