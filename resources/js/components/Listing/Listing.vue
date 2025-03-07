<script>
import {
    AisClearRefinements,
    AisConfigure,
    AisCurrentRefinements,
    AisHierarchicalMenu,
    AisHits,
    AisHitsPerPage,
    AisInstantSearch,
    AisPagination,
    AisRangeInput,
    AisRefinementList,
    AisSearchBox,
    AisSortBy,
    AisStats,
} from 'vue-instantsearch'
import Client from '@searchkit/instantsearch-client'
import Searchkit from 'searchkit'
import deepmerge from 'deepmerge'

import { history } from 'instantsearch.js/es/lib/routers'

Vue.component('ais-instant-search', AisInstantSearch)
Vue.component('ais-configure', AisConfigure)
Vue.component('ais-refinement-list', AisRefinementList)
Vue.component('ais-hierarchical-menu', AisHierarchicalMenu)
Vue.component('ais-range-input', AisRangeInput)
Vue.component('ais-search-box', AisSearchBox)
Vue.component('ais-current-refinements', AisCurrentRefinements)
Vue.component('ais-clear-refinements', AisClearRefinements)
Vue.component('ais-hits', AisHits)
Vue.component('ais-hits-per-page', AisHitsPerPage)
Vue.component('ais-sort-by', AisSortBy)
Vue.component('ais-pagination', AisPagination)
Vue.component('ais-stats', AisStats)

import categoryFilter from './Filters/CategoryFilter.vue'
import useAttributes from '../../stores/useAttributes.js'

export default {
    props: {
        sortOptionsCallback: {
            type: Function,
        },
        index: {
            type: String,
        },

        // TODO: Document these two props in the Rapidez docs
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
        attributes: useAttributes(),

        searchkit: null,
        searchClient: null,
        sortBy: null,
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    mounted() {
        this.searchkit = this.initSearchkit()
        this.searchClient = this.initSearchClient()

        this.loaded = Object.keys(this.attributes).length > 0
    },

    computed: {
        // TODO: Maybe move this completely to PHP?
        // Any drawbacks? A window.config that
        // becomes to big? Is that an issue?
        filters() {
            return Object.values(this.attributes)
                .filter((attribute) => attribute.filter)
                .map((filter) => ({ ...filter, code: this.filterPrefix(filter) + filter.code, base_code: filter.code }))
                .sort((a, b) => a.position - b.position)
        },

        facets() {
            return [
                ...this.filters.map((filter) => ({
                    attribute: filter.code,
                    field: filter.code + (this.filterType(filter) == 'string' ? '.keyword' : ''),
                    type: this.filterType(filter),
                })),
                ...this.categoryAttributes.map((attribute) => ({
                    attribute: attribute,
                    field: attribute + '.keyword',
                    type: 'string',
                })),
            ]
            // TODO: Double check this and how it's used.
            // .concat(this.additionalFilters)
        },

        categoryAttributes() {
            return Array.from({ length: config.max_category_level ?? 3 }).map((_, index) => 'category_lvl' + (index + 1))
        },

        sortings() {
            return Object.values(this.attributes)
                .filter((attribute) => attribute.sorting)
                .concat(
                    Object.entries(config.searchkit.additional_sorting).map(([code, directions]) => ({
                        code: code,
                        directions: directions,
                    })),
                )
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

        sortOptions() {
            let sortOptions = [
                {
                    label: config.translations.relevance,
                    field: '_score',
                    order: 'desc',
                    value: this.index,
                    key: 'default',
                },
            ].concat(
                this.sortings.flatMap((sorting) =>
                    (sorting.directions ?? ['asc', 'desc']).map((direction) => {
                        let label = ''
                        if (config.translations.sorting?.[sorting.code]?.[direction]) {
                            label = config.translations.sorting?.[sorting.code]?.[direction]
                        } else {
                            label = config.translations[sorting.code] ?? sorting.name ?? sorting.code

                            // Add asc/desc if relevant
                            if (sorting.directions?.length != 1) {
                                label += ' ' + (config.translations[direction] ?? direction)
                            }
                        }

                        return {
                            label: label,
                            field: sorting.code + (sorting.input == 'text' ? '.keyword' : ''),
                            order: direction,
                            value: [this.index, sorting.code, direction].join('_'),
                            key: '_' + [sorting.code, direction].join('_'),
                        }
                    }),
                ),
            )

            if (this.sortOptionsCallback) {
                sortOptions = this.sortOptionsCallback(sortOptions)
            }

            return sortOptions
        },

        routing() {
            return {
                router: history(),
                stateMapping: {
                    routeToState: this.routeToState,
                    stateToRoute: this.stateToRoute,
                },
            }
        },

        // TODO: Do we want to make this extendable?
        rangeAttributes() {
            return this.filters.filter((filter) => filter.input == 'price').map((filter) => filter.code)
        },
    },

    watch: {
        attributes(value) {
            this.loaded = Object.keys(value).length > 0
        },
    },

    methods: {
        // TODO: Not sure if this is the right place,
        // the autocomplete also needs this but
        // we don't want to load everything
        // directly due the JS size
        initSearchClient() {
            return Client(this.searchkit, {
                getBaseFilters: this.getBaseFilters,
                getQuery: this.query,
            })
        },

        initSearchkit() {
            let url = new URL(config.es_url)

            return new Searchkit({
                connection: {
                    host: url.origin,
                    auth: {
                        username: url.username,
                        password: url.password,
                    },
                },

                // TODO: Maybe just do: search_settings: config.searchkit
                // so it's possible to add anything to the PHP config
                // and that will appear here?
                search_settings: {
                    highlight_attributes: config.searchkit.highlight_attributes,
                    search_attributes: config.searchkit.search_attributes,
                    result_attributes: config.searchkit.result_attributes,

                    // TODO: For consistency maybe make it possible to do this:
                    // facet_attributes: config.searchkit.facet_attributes,
                    facet_attributes: this.facets,

                    filter_attributes: config.searchkit.filter_attributes,

                    sorting: this.sortOptions.reduce((acc, item) => {
                        acc[item.key] = {
                            field: item.field,
                            order: item.order,
                        }
                        return acc
                    }),
                },
            })
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

            let options = {}
            if (data.hitsPerPage != config.grid_per_page) {
                options['hitsPerPage'] = data.hitsPerPage
            }
            if (data.page > 1) {
                options['page'] = data.page
            }
            if (data.sortBy) {
                options['sortBy'] = data.sortBy.replace(this.index, '')
            }

            let parameters = {
                ...(data.range || {}),
                ...(data.refinementList || {}),
                options: options,
            }

            if ('query' in data) {
                parameters['q'] = data['query']
            }

            return parameters
        },

        routeToState(routeState) {
            let options = routeState.options ?? {}
            if ('sortBy' in options) {
                options.sortBy = this.index + options.sortBy
                this.sortBy = options.sortBy
            }

            let ranges = Object.fromEntries(Object.entries(routeState).filter(([key, _]) => this.rangeAttributes.includes(key)))

            let refinementList = Object.fromEntries(
                Object.entries(routeState).filter(([key, _]) => key != 'q' && !this.rangeAttributes.includes(key)),
            )

            return {
                [this.index]: {
                    refinementList: refinementList,
                    range: ranges,
                    query: routeState.q,
                    ...options,
                },
            }
        },

        filterType(filter) {
            if (filter.super) {
                return 'numeric'
            }

            return ['price', 'boolean'].includes(filter.input) ? 'numeric' : 'string'
        },

        filterPrefix(filter) {
            if (filter.super) {
                return 'super_'
            }

            if (filter.visual_swatch) {
                return 'visual_'
            }

            return ''
        },

        withFilters(items) {
            return items
                .map((item) => ({
                    filter: this.filters.find((filter) => filter.code === item.attribute),
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
