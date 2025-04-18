<script>
import Client from '@searchkit/instantsearch-client'
import Searchkit from 'searchkit'

export default {
    data: () => ({
        searchkit: null,
        searchClient: null,
    }),
    async mounted() {
        this.searchkit = await this.initSearchkit()
        this.searchClient = await this.initSearchClient()
    },
    methods: {
        async initSearchClient() {
            return Client(this.searchkit, await this.getInstantSearchClientConfig())
        },

        async initSearchkit() {
            let url = new URL(config.es_url)

            return new Searchkit({
                connection: {
                    host: url.origin,
                    auth: {
                        username: url.username,
                        password: url.password,
                    },
                },

                search_settings: await this.getSearchSettings(),
            })
        },

        async getInstantSearchClientConfig() {
            return {}
        },

        async getSearchSettings() {
            return config.searchkit
        },
    },
}
</script>
