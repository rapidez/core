<script>
import { AisConfigure, AisHighlight, AisHits, AisIndex, AisInstantSearch, AisSearchBox } from 'vue-instantsearch'
import Client from '@searchkit/instantsearch-client'
import Searchkit from 'searchkit'

Vue.component('ais-instant-search', AisInstantSearch)
Vue.component('ais-search-box', AisSearchBox)
Vue.component('ais-hits', AisHits)
Vue.component('ais-index', AisIndex)
Vue.component('ais-configure', AisConfigure)
Vue.component('ais-highlight', AisHighlight)

export default {
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
                    filter_attributes: config.searchkit.filter_attributes,
                },
            })

            return searchkit
        },
    },
}
</script>
