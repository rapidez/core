<script>
import Client from '@searchkit/instantsearch-client'
import Searchkit from 'searchkit'
import { instantsearchMiddlewares } from '../../stores/useInstantsearchMiddlewares'

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
            // TODO: Maybe just do: return config.searchkit;
            // so it's possible to add anything to the PHP config
            // and that will appear here?
            return {
                highlight_attributes: config.searchkit.highlight_attributes,
                search_attributes: config.searchkit.search_attributes,
                result_attributes: config.searchkit.result_attributes,
            }
        },

        getMiddlewares() {
            return instantsearchMiddlewares;
        },
    },
    computed: {
        middlewares() {
            return this.getMiddlewares()
        },
    },
}
</script>
