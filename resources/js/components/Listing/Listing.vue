<script>
import {
    ReactiveBase,
    ReactiveList,
    ReactiveComponent,
    SelectedFilters,
    MultiList,
    DynamicRangeSlider,
} from '@appbaseio/reactivesearch-vue'
import VueSlider from 'vue-slider-component'
import categoryFilter from './Filters/CategoryFilter.vue'
import useAttributes from '../../stores/useAttributes.js'

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
            default: () => [],
        },
        additionalSorting: {
            type: Array,
            default: () => [],
        },
    },

    data: () => ({
        loaded: false,
        attributes: useAttributes(),
        pageSize: Turbo.navigator.location.searchParams.get('pageSize') || config.grid_per_page,
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    mounted() {
        this.loaded = Object.keys(this.attributes).length > 0
    },

    computed: {
        filters: function () {
            return Object.values(this.attributes)
                .filter((attribute) => attribute.filter)
                .sort((a, b) => a.position - b.position)
        },
        sortings: function () {
            return Object.values(this.attributes).filter((attribute) => attribute.sorting)
        },
        reactiveFilters: function () {
            return this.filters.map((filter) => filter.code).concat(this.additionalFilters)
        },
        sortOptions: function () {
            return [
                {
                    label: window.config.translations.relevance,
                    dataField: '_score',
                    sortBy: 'desc',
                },
            ]
                .concat(
                    this.sortings.flatMap(function (sorting) {
                        return [
                            ['asc', window.config.translations.asc],
                            ['desc', window.config.translations.desc],
                        ].map(function ([directionKey, directionLabel]) {
                            return {
                                label:
                                    window.config.translations.sorting?.[sorting.code]?.[directionKey] ??
                                    sorting.name + ' ' + directionLabel,
                                dataField: sorting.code + (sorting.code != 'price' ? '.keyword' : ''),
                                sortBy: directionKey,
                            }
                        })
                    }),
                )
                .concat(this.additionalSorting)
        },
    },
    watch: {
        attributes: function (value) {
            this.loaded = Object.keys(value).length > 0
        },

        pageSize: function (pageSize) {
            let url = new URL(window.location)
            url.searchParams.set('pageSize', pageSize)
            window.history.pushState(window.history.state, '', url)
        },
    },

    methods: {
        getQuery() {
            if (!window.config.category?.entity_id) {
                return
            }

            return {
                query: {
                    function_score: {
                        script_score: {
                            script: {
                                source: Integer.parseInt(
                                    doc['positions.' + window.config.category.entity_id].empty
                                        ? '0'
                                        : doc['positions.' + window.config.category.entity_id + ''].value,
                                ),
                            },
                        },
                    },
                },
            }
        },
    },
}
</script>
