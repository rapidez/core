<script>
import InstantSearch from 'vue-instantsearch'
import Client from '@searchkit/instantsearch-client'
import Searchkit from 'searchkit'
import deepmerge from 'deepmerge'

import { history as historyRouter } from 'instantsearch.js/es/lib/routers'
import { singleIndex as singleIndexMapping } from 'instantsearch.js/es/lib/stateMappings'
// We should only import the components used!
// https://www.algolia.com/doc/guides/building-search-ui/installation/vue/?client=Vue+2#optimize-your-build-with-tree-shaking
Vue.use(InstantSearch)

import categoryFilter from './Filters/CategoryFilter.vue'
import useAttributes from '../../stores/useAttributes.js'

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
        pageSize:
            (Turbo?.navigator?.location?.searchParams || new URLSearchParams(window.location.search)).get('pageSize') ||
            config.grid_per_page,

        // TODO: We need some finetuning here; the url isn't very clean.
        // Also after a refresh the filters aren't selected.
        // Maybe it conflicts with ReactiveSearch?
        routing: {
            router: historyRouter(),
            stateMapping: singleIndexMapping('products_1'),
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
                            sr.body.query.bool.filter.push(this.getQuery())
                            return sr
                        })
                    },
                },
            })

            // console.log(client)

            return client
        },

        searchkit: function () {
            let searchkit = new Searchkit({
                connection: {
                    host: config.es_url,
                },
                search_settings: {
                    // Are we using this? In the autocomplete maybe?
                    // highlight_attributes: ['title'],

                    search_attributes: Object.entries(config.searchable).map(([field, weight]) => ({ field, weight })),

                    // We could make the response smaller with this
                    // result_attributes: ['title', 'actors', 'poster', 'plot'],

                    facet_attributes: this.facets,

                    filter_attributes: [
                        { attribute: 'category_ids', field: 'category_ids', type: 'numeric' },
                        { attribute: 'visibility', field: 'visibility', type: 'numeric' },
                    ],

                    sorting: {
                        default: {
                            field: '_score',
                            order: 'desc',
                        },
                        _rated_desc: {
                            field: 'rated',
                            order: 'desc',
                        },
                    },
                },
            })

            // console.log(this.facets)

            return searchkit
        },

        filters: function () {
            return Object.values(this.attributes)
                .filter((attribute) => attribute.filter)
                .sort((a, b) => a.position - b.position)
        },

        facets: function () {
            return [
                ...this.filters.map((filter) => ({
                    attribute: filter.code,
                    field: filter.code + (['price', 'boolean'].includes(filter.input) ? '' : '.keyword'),
                    type: ['price', 'boolean'].includes(filter.input) ? 'numeric' : 'string',
                })),
                { attribute: 'category_lvl0', field: 'category_lvl0.keyword', type: 'string' },
                { attribute: 'category_lvl1', field: 'category_lvl1.keyword', type: 'string' },
                { attribute: 'category_lvl2', field: 'category_lvl2.keyword', type: 'string' },
                { attribute: 'category_lvl3', field: 'category_lvl3.keyword', type: 'string' },
            ]

            return this.filters
                .map((filter) => ({
                    attribute: filter.code,
                    field: filter.code + (['price', 'boolean'].includes(filter.input) ? '' : '.keyword'),
                    type: ['price', 'boolean'].includes(filter.input) ? 'numeric' : 'string',
                }))
                .push([
                    { attribute: 'category_lvl0', field: 'category_lvl0.keyword', type: 'string' },
                    { attribute: 'category_lvl1', field: 'category_lvl1.keyword', type: 'string' },
                    { attribute: 'category_lvl2', field: 'category_lvl2.keyword', type: 'string' },
                    { attribute: 'category_lvl3', field: 'category_lvl3.keyword', type: 'string' },
                ])
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
                        default: index === 0,
                    }
                })
                .concat({ label: this.$root.config.translations.all, value: 10000 })
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
    },
}
</script>
