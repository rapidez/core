<!-- TODO: Is this one still in use and up-to-date? -->
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

    computed: {
        activeFilters() {
            return Object.keys(this.selectedValues)
                .filter((filterKey) => {
                    let { showFilter, value } = this.selectedValues[filterKey]
                    return showFilter && (Array.isArray(value) ? value.length : !!value)
                })
                .reduce((result, filterKey) => {
                    let { value } = this.selectedValues[filterKey]

                    if (filterKey === 'category') {
                        return result.concat({ code: filterKey, value: config.subcategories[value] })
                    }

                    if (filterKey === 'price') {
                        let [minPrice, maxPrice] = value
                        return result.concat({ code: filterKey, value: price(minPrice) + ' - ' + price(maxPrice) })
                    }

                    if (Array.isArray(value)) {
                        // Check if the value is a swatch value, boolean or just an array
                        let items = Object.keys(swatches.value).includes(filterKey)
                            ? value.map((selected) => swatches.value[filterKey].options[selected].label)
                            : value.map((item) =>
                                  item === '0'
                                      ? window.config.translations.filters.no
                                      : item === '1'
                                        ? window.config.translations.filters.yes
                                        : item,
                              )

                        // We can have a filter where multiple values may be selected, so we need to map and concat all of them
                        return result.concat(items.map((item) => ({ code: filterKey, value: item })))
                    }

                    return result
                }, [])
        },
    },
}
</script>
