<script>
import { useEventListener } from '@vueuse/core'
import InstantSearchMixin from './InstantSearchMixin.vue'

import InstantSearch from 'vue-instantsearch/vue3/es/src/components/InstantSearch'
import Hits from 'vue-instantsearch/vue3/es/src/components/Hits.js'
import Configure from 'vue-instantsearch/vue3/es/src/components/Configure.js'
import Autocomplete from 'vue-instantsearch/vue3/es/src/components/Autocomplete.vue.js'
import Index from 'vue-instantsearch/vue3/es/src/components/Index.js'
import Stats from 'vue-instantsearch/vue3/es/src/components/Stats.vue.js'
import StateResults from 'vue-instantsearch/vue3/es/src/components/StateResults.vue.js'
import StatsAnalytics from './AisStatsAnalytics.vue'

import { useDebounceFn } from '@vueuse/core'
import { rapidezAPI } from '../../fetch'
import { searchHistory } from '../../stores/useSearchHistory'

let focusId = document.activeElement.id
export default {
    mixins: [InstantSearchMixin],
    components: {
        InstantSearch,
        Hits,
        Configure,
        highlight,
        Autocomplete,
        Index,
        Stats,
        StateResults,
        StatsAnalytics,
    },

    props: {
        hitsPerPage: {
            type: Number,
            default: 3,
        },
        filterQueryString: {
            type: String,
        },
    },

    render() {
        return this.$slots.default(this)
    },
    created() {
        focusId ??= document.activeElement.id
    },
    mounted() {
        let element = null
        if (focusId && (element = this.$el.nextSibling.querySelector('#' + focusId))) {
            setTimeout(() => {
                requestAnimationFrame(() => {
                    element?.focus()
                })
            })
        }
        this.$nextTick(() => {
            this.$emit('mounted')
            setTimeout(() => {
                requestAnimationFrame(() => {
                    let element = null
                    if (focusId && (element = this.$el.nextSibling.querySelector('#' + focusId))) {
                        element?.focus()
                    }
                })
            })
        })

        const stateChanged = useDebounceFn((event) => {
            const query = event?.payload?.query

            if (!query) {
                return
            }

            rapidezAPI('post', '/search', {
                q: query,
                results: event?.payload?.nbHits,
            })
        }, 3000)

        useEventListener(this.$el.nextSibling, 'insights-event:viewedObjectIDs', (event) => {
            const insightsEvent = event.detail.insightsEvent
            if (insightsEvent?.eventType !== 'search') {
                return
            }

            stateChanged(insightsEvent)
        })
    },

    computed: {
        searchHistory() {
            return Object.entries(searchHistory.value).sort((a, b) => {
                return Date.parse(b[1].lastSearched) - Date.parse(a[1].lastSearched)
            })
        },
    },
    methods: {
        async initSearchClient() {
            const client = await InstantSearchMixin.methods.initSearchClient.bind(this).call()

            // Ensure no query is done if the search field is empty
            const oldSearch = client.search
            client.search = async (requests) => {
                if (requests.every(({ params }) => !params.query)) {
                    return Promise.resolve({
                        results: requests.map(() => ({
                            hits: [],
                            nbHits: 0,
                            nbPages: 0,
                            page: 0,
                            processingTimeMS: 0,
                            hitsPerPage: 0,
                            exhaustiveNbHits: false,
                            query: '',
                            params: '',
                        })),
                    })
                }

                requests = requests.map((request) => {
                    request.params.hitsPerPage = request.params.hitsPerPage || this.hitsPerPage

                    return request
                })

                return oldSearch.bind(client)(requests)
            }

            return client
        },

        async getInstantSearchClientConfig() {
            const config = await InstantSearchMixin.methods.getInstantSearchClientConfig.bind(this).call()

            config.getBaseFilters = this.getBaseFilters

            return config
        },

        getBaseFilters() {
            let extraFilters = []
            extraFilters.push({
                query_string: {
                    query: 'visibility:(3 OR 4) OR (NOT _exists_:visibility)',
                },
            })

            if (this.filterQueryString) {
                extraFilters.push({
                    query_string: {
                        query: this.filterQueryString,
                    },
                })
            }

            return extraFilters
        },
    },
}
</script>
