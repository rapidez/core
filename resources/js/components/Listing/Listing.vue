<script>
import { history } from 'instantsearch.js/es/lib/routers'
import useAttributes from '../../stores/useAttributes.js'
import InstantSearchMixin from '../Search/InstantSearchMixin.vue'

export default {
    mixins: [InstantSearchMixin],
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

        // TODO: Document these Four props in the Rapidez docs
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
        this.loaded = Object.keys(this.attributes).length > 0
        if (this.isSearchPage) {
            document.title = config.translations.search.title + ': ' + this.$root.queryParams.get('q')
        }
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
                    windowTitle: this.windowTitle,
                }),
                stateMapping: {
                    routeToState: this.routeToState,
                    stateToRoute: this.stateToRoute,
                },
            }
        },

        isSearchPage: function () {
            return this.$root.queryParams.has('q')
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
        async getInstantSearchClientConfig() {
            const config = await InstantSearchMixin.methods.getInstantSearchClientConfig.bind(this).call()

            config.getBaseFilters = this.getBaseFilters
            config.getQuery = this.query

            return config
        },

        async getSearchSettings() {
            let config = await InstantSearchMixin.methods.getSearchSettings.bind(this).call()

            return {
                ...config,
                filter_attributes: window.config.searchkit.filter_attributes,

                // TODO: For consistency maybe make it possible to do this:
                // facet_attributes: config.searchkit.facet_attributes,
                facet_attributes: this.facets,

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
            }
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
