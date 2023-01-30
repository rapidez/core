<script>
    import {
        ReactiveBase,
        ReactiveList,
        ReactiveComponent,
        SelectedFilters,
        MultiList,
        DynamicRangeSlider
    } from '@appbaseio/reactivesearch-vue'
    import VueSlider from 'vue-slider-component'
    import categoryFilter from './Filters/CategoryFilter.vue'
    import { useLocalStorage } from '@vueuse/core'

    Vue.use(ReactiveBase)
    Vue.use(ReactiveList)
    Vue.use(ReactiveComponent)
    Vue.use(SelectedFilters)
    Vue.use(MultiList)
    Vue.component('vue-slider-component', VueSlider)
    Vue.use(DynamicRangeSlider)
    Vue.component('category-filter', categoryFilter)

    export default {
        props: {
            additionalFilters: {
                type: Array,
                default: () => []
            },
            additionalSorting: {
                type: Array,
                default: () => []
            }
        },

        data: () => ({
            loaded: false,
            attributes: useLocalStorage('attributes', {}),
        }),

        render() {
            return this.$scopedSlots.default({
                loaded: this.loaded,
                filters: this.filters,
                sortOptions: this.sortOptions,
                reactiveFilters: this.reactiveFilters,
            })
        },

        mounted() {
            if (Object.keys(this.attributes).length) {
                this.loaded = true
                return;
            }

            axios.get('/api/attributes')
                 .then((response) => {
                    this.attributes = response.data
                    this.loaded = true
                 })
                 .catch((error) => {
                    Notify(window.config.errors.wrong, 'error', error.response.data?.parameters)
                })
        },

        computed: {
            filters: function () {
                return Object.values(this.attributes)
                    .filter((attribute) => attribute.filter)
                    .sort((a, b) => a.position - b.position);
            },
            sortings: function () {
                return Object.values(this.attributes)
                    .filter((attribute) => attribute.sorting)
            },
            reactiveFilters: function () {
                return this.filters
                    .map((filter) => filter.code)
                    .concat(this.additionalFilters);
            },
            sortOptions: function () {
                return [
                    {
                        label: window.config.translations.relevance,
                        dataField: '_score',
                        sortBy: 'desc'
                    }
                ].concat(this.sortings.flatMap(function (sorting) {
                    return [
                        ['asc', window.config.translations.asc],
                        ['desc', window.config.translations.desc]
                    ].map(function ([directionLabel, directionKey]) {
                        return {
                            label: window.config.translations.sorting?.[sorting.code]?.[directionKey] ?? sorting.name + ' ' + directionLabel,
                            dataField: sorting.code + (sorting.code != 'price' ? '.keyword' : ''),
                            sortBy: directionKey
                        }
                    })
                })).concat(this.additionalSorting)
            }
        }
    }
</script>
