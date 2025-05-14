<script>
import InstantSearchMixin from './InstantSearchMixin.vue'

import InstantSearch from 'vue-instantsearch/vue2/es/src/components/InstantSearch'
import Hits from 'vue-instantsearch/vue2/es/src/components/Hits.js'
import Configure from 'vue-instantsearch/vue2/es/src/components/Configure.js'
import highlight from 'vue-instantsearch/vue2/es/src/components/Highlight.vue.js'
import SearchBox from 'vue-instantsearch/vue2/es/src/components/SearchBox.vue.js'
import Index from 'vue-instantsearch/vue2/es/src/components/Index.js'
import { useDebounceFn } from '@vueuse/core'
import { rapidezAPI } from '../../fetch'
import { searchHistory } from '../../stores/useSearchHistory'

export default {
    mixins: [InstantSearchMixin],
    components: {
        InstantSearch,
        Hits,
        Configure,
        highlight,
        SearchBox,
        Index,
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    mounted() {
        this.$nextTick(() => {
            requestAnimationFrame(() => this.$emit('mounted'))
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

        this.$on('insights-event:viewedObjectIDs', (event) => {
            if (event?.eventType !== 'search') {
                return
            }

            stateChanged(event)
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

                return oldSearch.bind(client)(requests)
            }

            return client
        },

        getMiddlewares() {
            let middlewares = InstantSearchMixin.methods.getMiddlewares.bind(this).call()

            const stateChanged = useDebounceFn((changes) => {
                const query = Object.entries(changes.uiState).find(([id, state]) => {
                    return state?.query
                })?.[1]?.query

                if (!query) {
                    return
                }

                rapidezAPI('post', '/search', {
                    q: query,
                })
            }, 3000)

            return [
                ...middlewares,
                () => ({
                    onStateChange(changes) {
                        stateChanged(changes)
                    },
                }),
            ]
        },
    },
}
</script>
