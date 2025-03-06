<script>
// TODO: How can we have this extendable in
// case we want to use another component?
import Client from '@searchkit/instantsearch-client'
import Searchkit from 'searchkit'
import deepmerge from 'deepmerge'

import { history } from 'instantsearch.js/es/lib/routers'
import { simple } from 'instantsearch.js/es/lib/stateMappings'

// This is purely done for the ability to preload these files when importing Listing.vue with the @vite directive
import AisInstantSearch from 'vue-instantsearch/vue2/es/src/components/InstantSearch'
import AisSearchBox from 'vue-instantsearch/vue2/es/src/components/SearchBox.vue.js'
import AisHits from 'vue-instantsearch/vue2/es/src/components/Hits.js'
import AisIndex from 'vue-instantsearch/vue2/es/src/components/Index.js'
import AisConfigure from 'vue-instantsearch/vue2/es/src/components/Configure.js'
import AisHighlight from 'vue-instantsearch/vue2/es/src/components/Highlight.vue.js'

import AisRefinementList from 'vue-instantsearch/vue2/es/src/components/RefinementList.vue.js'
import AisHierarchicalMenu from 'vue-instantsearch/vue2/es/src/components/HierarchicalMenu.vue.js'
import AisRangeInput from 'vue-instantsearch/vue2/es/src/components/RangeInput.vue.js'
import AisCurrentRefinements from 'vue-instantsearch/vue2/es/src/components/CurrentRefinements.vue.js'
import AisClearRefinements from 'vue-instantsearch/vue2/es/src/components/ClearRefinements.vue.js'
import AisHitsPerPage from 'vue-instantsearch/vue2/es/src/components/HitsPerPage.vue.js'
import AisSortBy from 'vue-instantsearch/vue2/es/src/components/SortBy.vue.js'
import AisPagination from 'vue-instantsearch/vue2/es/src/components/Pagination.vue.js'
import AisStats from 'vue-instantsearch/vue2/es/src/components/Stats.vue.js'

Vue.component('ais-instant-search', AisInstantSearch)
Vue.component('ais-search-box', AisSearchBox)
Vue.component('ais-hits', AisHits)
Vue.component('ais-index', AisIndex)
Vue.component('ais-configure', AisConfigure)
Vue.component('ais-highlight', AisHighlight)

Vue.component('ais-refinement-list', AisRefinementList)
Vue.component('ais-hierarchical-menu', AisHierarchicalMenu)
Vue.component('ais-range-input', AisRangeInput)
Vue.component('ais-current-refinements', AisCurrentRefinements)
Vue.component('ais-clear-refinements', AisClearRefinements)
Vue.component('ais-hits-per-page', AisHitsPerPage)
Vue.component('ais-sort-by', AisSortBy)
Vue.component('ais-pagination', AisPagination)
Vue.component('ais-stats', AisStats)

import categoryFilter from './Filters/CategoryFilter.vue'
import useAttributes from '../../stores/useAttributes.js'

export default {
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
    },

    data: () => ({
        loaded: false,
        attributes: useAttributes(),

        // TODO: Not sure yet if this is the right option,
        // when the routing works properly we maybe
        // don't need this.
        searchTerm: new URLSearchParams(window.location.search).get('q'),

        // TODO: We need some finetuning here; the url isn't very clean.
        // Also after a refresh the filters aren't selected.
        // Maybe it conflicts with ReactiveSearch?
        routing: {
            router: history(),
            // stateMapping: singleIndex('rapidez_product_1'),
            stateMapping: simple(),
        },
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    mounted() {
        this.loaded = Object.keys(this.attributes).length > 0
    },

    computed: {
        // TODO: Not sure if this is the right place,
        // the autocomplete also needs this but
        // we don't want to load everything
        // directly due the JS size
        searchClient: function () {
            let client = Client(this.searchkit, {
                hooks: {
                    beforeSearch: async (searchRequests) => {
                        return searchRequests.map((sr) => {
                            // TODO: Maybe use deepmerge here so it doesn't
                            // really matter what query is used? What
                            // if we want to add something to the
                            // "must" instead of "filter"?
                            if (this.getQuery()) {
                                sr.body.query.bool.filter.push(this.getQuery())
                            }
                            // And, this is currently applied on all queries,
                            // it's only relevant on the listing one.
                            return sr
                        })
                    },
                },
            })

            // console.log(client)

            return client
        },

        searchkit: function () {
            let url = new URL(config.es_url)

            let searchkit = new Searchkit({
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

            // console.log(this.sortOptions)

            return searchkit
        },

        // TODO: Maybe move this completely to PHP?
        // Any drawbacks? A window.config that
        // becomes to big? Is that an issue?
        filters: function () {
            return Object.values(this.attributes)
                .filter((attribute) => attribute.filter)
                .map((filter) => ({ ...filter, code: this.filterPrefix(filter) + filter.code, base_code: filter.code }))
                .sort((a, b) => a.position - b.position)
        },

        facets: function () {
            return [
                ...this.filters.map((filter) => ({
                    attribute: filter.code,
                    field: filter.code + (this.filterType(filter) == 'string' ? '.keyword' : ''),
                    type: this.filterType(filter),
                })),
                { attribute: 'category_lvl1', field: 'category_lvl1.keyword', type: 'string' },
                { attribute: 'category_lvl2', field: 'category_lvl2.keyword', type: 'string' },
                { attribute: 'category_lvl3', field: 'category_lvl3.keyword', type: 'string' },
            ]
            // TODO: Double check this and how it's used.
            // .concat(this.additionalFilters)
        },

        sortings: function () {
            return Object.values(this.attributes).filter((attribute) => attribute.sorting)
        },

        hitsPerPage: function () {
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

        sortOptions: function () {
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
    },

    watch: {
        attributes: function (value) {
            this.loaded = Object.keys(value).length > 0
        },
    },

    methods: {
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

        getQuery() {
            if (!window.config.category?.entity_id) {
                return
            }

            return {
                // query: {
                function_score: {
                    script_score: {
                        script: {
                            source: `Integer.parseInt(doc['positions.${window.config.category.entity_id}'].empty ? '0' : doc['positions.${window.config.category.entity_id}'].value)`,
                        },
                    },
                },
                // },
            }
        },

        withFilters(items) {
            return items.map((item) => ({
                filter: this.filters.find((filter) => filter.code === item.attribute),
                ...item,
            }))
        },

        withSwatches(items, filter) {
            return [];
            items.map((item) => ({
                swatch: this.$root.swatches[filter.base_code]?.options?.[item.value] ?? null,
                ...item,
            }))
        },
    },
}
</script>
