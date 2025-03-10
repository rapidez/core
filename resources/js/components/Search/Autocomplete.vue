<script>
import Client from '@searchkit/instantsearch-client'
import Searchkit from 'searchkit'

import AisInstantSearch from 'vue-instantsearch/vue2/es/src/components/InstantSearch'
import AisSearchBox from 'vue-instantsearch/vue2/es/src/components/SearchBox.vue.js'
import AisHits from 'vue-instantsearch/vue2/es/src/components/Hits.js'
import AisIndex from 'vue-instantsearch/vue2/es/src/components/Index.js'
import AisConfigure from 'vue-instantsearch/vue2/es/src/components/Configure.js'
import AisHighlight from 'vue-instantsearch/vue2/es/src/components/Highlight.vue.js'

export default {
    components: {
        'ais-instant-search': AisInstantSearch,
        'ais-search-box': AisSearchBox,
        'ais-hits': AisHits,
        'ais-index': AisIndex,
        'ais-configure': AisConfigure,
        'ais-highlight': AisHighlight,
    },
    data: () => ({
        loaded: false,
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    mounted() {
        this.$nextTick(() => {
            this.$emit('mounted')
            this.loaded = true
        })
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
                        return searchRequests
                    },
                },
            })

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

        // TODO: Maybe extract this so we have this once?
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

                // TODO: Should we split this as it could
                // diff from the settings on listings.
                search_settings: {
                    highlight_attributes: config.searchkit.highlight_attributes,
                    search_attributes: config.searchkit.search_attributes,
                    result_attributes: config.searchkit.result_attributes,
                    filter_attributes: config.searchkit.filter_attributes,
                },
            })

            return searchkit
        },
    },
}
</script>
