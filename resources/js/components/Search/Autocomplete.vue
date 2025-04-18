<script>
import InstantSearchMixin from './InstantSearchMixin.vue'

import InstantSearch from 'vue-instantsearch/vue2/es/src/components/InstantSearch'
import Hits from 'vue-instantsearch/vue2/es/src/components/Hits.js'
import Configure from 'vue-instantsearch/vue2/es/src/components/Configure.js'
import highlight from 'vue-instantsearch/vue2/es/src/components/Highlight.vue.js'
import SearchBox from 'vue-instantsearch/vue2/es/src/components/SearchBox.vue.js'
import Index from 'vue-instantsearch/vue2/es/src/components/Index.js'

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
    },
}
</script>
