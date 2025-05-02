<script>
import { history } from 'instantsearch.js/es/lib/routers'
import InstantSearchMixin from '../Search/InstantSearchMixin.vue'

export default {
    mixins: [InstantSearchMixin],
    props: {
        index: {
            type: String,
            default: window.config.index.product
        },
        query: {
            type: Function,
        },
        categoryId: {
            type: Number,
        },
        baseFilters: {
            type: Function,
            default: () => [],
        },
        filterQueryString: {
            type: String,
        },
        configCallback: {
            type: Function,
        },
    },

    data: () => ({
        searchkit: null,
        searchClient: null,
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    computed: {
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
                    windowTitle: this.windowTitle,
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

            return this.configCallback ? this.configCallback(config) : config
        },

        getBaseFilters() {
            if (this.categoryId) {
                return this.baseFilters().concat([
                    { query_string: { query: 'visibility:(2 OR 4) AND category_ids:' + this.categoryId } },
                    this.$root.categoryPositions(this.categoryId),
                ])
            }

            let extraFilters = []
            if (this.filterQueryString) {
                extraFilters.push({
                    query_string: {
                        query: this.filterQueryString,
                    },
                })
            }

            return this.baseFilters().concat(extraFilters)
        },

        windowTitle(routeState) {
            if (!routeState.q) {
                return window.config.translations.search.title
            }

            return window.config.translations.search.title + ': ' + routeState.q
        },

        stateToRoute(uiState) {
            let data = uiState[this.index]

            return {
                ...(data.range || {}),
                ...(data.refinementList || {}),
                category: data.hierarchicalMenu?.category_lvl1?.join('--'),
                q: data.query,
                page: data.page > 0 ? String(data.page) : undefined,
                sort: data.sortBy,
                hits: data.hitsPerPage != config.grid_per_page ? data.hitsPerPage : undefined,
            }
        },

        routeToState(routeState) {
            let ranges = Object.fromEntries(Object.entries(routeState).filter(([key]) => this.rangeAttributes.includes(key)))

            let refinementList = Object.fromEntries(
                Object.entries(routeState).filter(
                    ([key]) => !['q', 'hits', 'sort', 'page', 'category'].includes(key) && !this.rangeAttributes.includes(key),
                ),
            )

            return {
                [this.index]: {
                    range: ranges,
                    refinementList: refinementList,
                    hierarchicalMenu: { category_lvl1: routeState.category?.split('--') },
                    query: routeState.q,
                    page: Number(routeState.page),
                    sortBy: routeState.sort,
                    hitsPerPage: Number(routeState.hits),
                },
            }
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
                swatch: window.config.swatches[filter?.base_code]?.options?.[item.value] ?? null,
                ...item,
            }))
        },
    },
}
</script>
