<script>
import AisSearchBox from 'vue-instantsearch/vue2/es/src/components/SearchBox.vue.js'
import AisIndex from 'vue-instantsearch/vue2/es/src/components/Index.js'

import InstantSearchMixin from './InstantSearchMixin.vue'

export default {
    mixins: [InstantSearchMixin],
    components: {
        'ais-search-box': AisSearchBox,
        'ais-index': AisIndex,
    },
    data: () => ({
        loaded: false,
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    mounted() {
        this.$nextTick(() => {
            requestAnimationFrame(() => this.$emit('mounted'))
            this.loaded = true
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
