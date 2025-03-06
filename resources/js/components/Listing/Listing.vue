<script>
// TODO: How can we have this extendable in
// case we want to use another component?
import Client from '@searchkit/instantsearch-client'
import Searchkit from 'searchkit'

import { history } from 'instantsearch.js/es/lib/routers'

import AisInstantSearch from 'vue-instantsearch/vue2/es/src/components/InstantSearch'
import AisSearchBox from 'vue-instantsearch/vue2/es/src/components/SearchBox.vue.js'
import AisHits from 'vue-instantsearch/vue2/es/src/components/Hits.js'
import AisConfigure from 'vue-instantsearch/vue2/es/src/components/Configure.js'

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
import useAttributes from '../../stores/useAttributes.js'

export default {
    components: {
        'ais-instant-search': AisInstantSearch,
        'ais-search-box': AisSearchBox,
        'ais-hits': AisHits,
        'ais-configure': AisConfigure,
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
        // TODO: Do we still use/need this?
        // Maybe transform it to a callback
        // so the items can be manipulated?
        additionalFilters: {
            type: Array,
            default: () => [],
        },
        additionalSorting: {
            type: Array,
            default: () => [],
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
        },
    },

    data: () => ({
        loaded: false,
        attributes: useAttributes(),

        searchkit: null,
        searchClient: null,
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
            return Object.values(this.attributes).filter((attribute) => attribute.sorting)
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
            return [
                {
                    label: window.config.translations.relevance,
                    field: '_score',
                    order: 'desc',
                    value: config.index,
                    key: 'default',
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
                                field: sorting.code + (sorting.code != 'price' ? '.keyword' : ''),
                                order: directionKey,
                                value: [config.index, sorting.code, directionKey].join('_'),
                                key: '_' + [sorting.code, directionKey].join('_'),
                            }
                        })
                    }),
                )
                .concat(this.additionalSorting)
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
                getBaseFilters: this.baseFilters,
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

                    // TODO: Let's also change this to a PHP config.
                    // So we start there and that will be merged
                    // with the Magento configured attributes
                    // and lastly from a prop it's possible
                    // to manipulate it from a callback?
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
