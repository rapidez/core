<script>
import { history } from 'instantsearch.js/es/lib/routers'

import AisSearchBox from 'vue-instantsearch/vue2/es/src/components/SearchBox.vue.js'
import AisRefinementList from 'vue-instantsearch/vue2/es/src/components/RefinementList.vue.js'
import AisHierarchicalMenu from 'vue-instantsearch/vue2/es/src/components/HierarchicalMenu.vue.js'
import AisRangeInput from 'vue-instantsearch/vue2/es/src/components/RangeInput.vue.js'
import AisCurrentRefinements from 'vue-instantsearch/vue2/es/src/components/CurrentRefinements.vue.js'
import AisClearRefinements from 'vue-instantsearch/vue2/es/src/components/ClearRefinements.vue.js'
import AisHitsPerPage from 'vue-instantsearch/vue2/es/src/components/HitsPerPage.vue.js'
import AisSortBy from 'vue-instantsearch/vue2/es/src/components/SortBy.vue.js'
import AisPagination from 'vue-instantsearch/vue2/es/src/components/Pagination.vue.js'
import AisStats from 'vue-instantsearch/vue2/es/src/components/Stats.vue.js'

import categoryFilter from './Filters/CategoryFilter.vue'

import InstantSearchMixin from '../Search/InstantSearchMixin.vue'

export default {
    mixins: [InstantSearchMixin],
    components: {
        'ais-search-box': AisSearchBox,
        'ais-refinement-list': AisRefinementList,
        'ais-hierarchical-menu': AisHierarchicalMenu,
        'ais-range-input': AisRangeInput,
        'ais-current-refinements': AisCurrentRefinements,
        'ais-clear-refinements': AisClearRefinements,
        'ais-hits-per-page': AisHitsPerPage,
        'ais-sort-by': AisSortBy,
        'ais-pagination': AisPagination,
        'ais-stats': AisStats,
    },
    props: {
        configCallback: {
            type: Function,
        },
        index: {
            type: String,
        },

        // TODO: Document these four props in the Rapidez docs
        query: {
            type: Function,
        },
        baseFilters: {
            type: Function,
            default: () => [],
        },
        filterQueryString: {
            type: String,
        },
        filterScoreScript: {
            type: String,
        },
    },

    data: () => ({
        loaded: false,

        searchkit: null,
        searchClient: null,
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    mounted() {
        this.loaded = true
    },

    computed: {
        facets() {
            return [
                ...config.filterable_attributes.map((filter) => ({
                    attribute: filter.code,
                    field: filter.code + (this.filterType(filter) == 'string' ? '.keyword' : ''),
                    type: this.filterType(filter),
                })),
                ...this.categoryAttributes.map((attribute) => ({
                    attribute: attribute,
                    field: attribute + '.keyword',
                    type: 'string',
                })),
            ].concat(config.searchkit.facet_attributes)
        },

        categoryAttributes() {
            return Array.from({ length: config.max_category_level ?? 3 }).map((_, index) => 'category_lvl' + (index + 1))
        },

        hitsPerPage() {
            return this.$root.config.grid_per_page_values
                .map(function (pages, index) {
                    return {
                        label: pages,
                        value: pages,
                        default: pages == config.grid_per_page,
                    }
                })
                .concat({ label: this.$root.config.translations.all, value: 10000 })
        },

        routing() {
            return {
                router: history({
                    cleanUrlOnDispose: false,
                }),
                stateMapping: {
                    routeToState: this.routeToState,
                    stateToRoute: this.stateToRoute,
                },
            }
        },

        rangeAttributes() {
            return config.filterable_attributes
                .filter((filter) => filter.input == 'price')
                .map((filter) => filter.code)
                .concat(config.searchkit.range_attributes ?? [])
        },
    },

    methods: {
        async getInstantSearchClientConfig() {
            const config = await InstantSearchMixin.methods.getInstantSearchClientConfig.bind(this).call()

            config.getBaseFilters = this.getBaseFilters
            config.getQuery = this.query

            return config
        },

        async getSearchSettings() {
            let config = await InstantSearchMixin.methods.getSearchSettings.bind(this).call()
            config = {
                ...config,
                facet_attributes: this.facets,
            }
            return this.configCallback ? this.configCallback(config) : config
        },

        getBaseFilters() {
            let extraFilters = []
            if (this.filterQueryString) {
                extraFilters.push({
                    query_string: {
                        query: this.filterQueryString,
                    },
                })
            }

            if (this.filterScoreScript) {
                extraFilters.push({
                    function_score: {
                        script_score: {
                            script: {
                                source: this.filterScoreScript,
                            },
                        },
                    },
                })
            }

            return this.baseFilters().concat(extraFilters)
        },

        stateToRoute(uiState) {
            let data = uiState[this.index]

            let parameters = {
                ...(data.range || {}),
                ...(data.refinementList || {}),
            }

            if ('query' in data) {
                parameters['q'] = data['query']
            }

            return parameters
        },

        routeToState(routeState) {
            let ranges = Object.fromEntries(Object.entries(routeState).filter(([key, _]) => this.rangeAttributes.includes(key)))

            let refinementList = Object.fromEntries(
                Object.entries(routeState).filter(([key, _]) => key != 'q' && !this.rangeAttributes.includes(key)),
            )

            return {
                [this.index]: {
                    refinementList: refinementList,
                    range: ranges,
                    query: routeState.q,
                },
            }
        },

        filterType(filter) {
            if (filter.super) {
                return 'numeric'
            }

            return ['price', 'boolean'].includes(filter.input) ? 'numeric' : 'string'
        },

        withFilters(items) {
            return items
                .map((item) => ({
                    filter: config.filterable_attributes.find((filter) => filter.code === item.attribute),
                    ...item,
                }))
                .filter((item) => item.filter)
        },

        withSwatches(items, filter) {
            return items.map((item) => ({
                swatch: this.$root.swatches[filter?.base_code]?.options?.[item.value] ?? null,
                ...item,
            }))
        },
    },
}
</script>
