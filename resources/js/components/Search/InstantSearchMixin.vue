<script>
import Client from '@searchkit/instantsearch-client'
import Searchkit from 'searchkit'
import { instantsearchMiddlewares } from '../../stores/useInstantsearchMiddlewares'
import { createInsightsMiddleware } from 'instantsearch.js/es/middlewares/createInsightsMiddleware'

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
            return {
                hooks: {
                    beforeSearch: async (searchRequests) => {
                        return searchRequests.map((sr) => {
                            sr.request.params.highlightPreTag = '<mark>'
                            sr.request.params.highlightPostTag = '</mark>'
                            return sr
                        })
                    },
                },
            }
        },

        async getSearchSettings() {
            return config.searchkit
        },

        getMiddlewares() {
            return instantsearchMiddlewares
        },

        relevanceQueryMatch(query, search_attributes, fuzziness = 'AUTO:4,8') {
            // Copied from searchkit.js default behavior when getQuery is not defined.
            const getFieldsMap = (boostMultiplier) => {
                return search_attributes.map((attribute) => {
                    return typeof attribute === 'string' ? attribute : `${attribute.field}^${(attribute.weight || 1) * boostMultiplier}`
                })
            }

            return {
                bool: {
                    should: [
                        {
                            bool: {
                                should: [
                                    {
                                        multi_match: {
                                            query,
                                            fields: getFieldsMap(1),
                                            fuzziness: fuzziness,
                                        },
                                    },
                                    {
                                        multi_match: {
                                            query,
                                            fields: getFieldsMap(0.5),
                                            type: 'bool_prefix',
                                        },
                                    },
                                ],
                            },
                        },
                        {
                            multi_match: {
                                query,
                                type: 'phrase',
                                fields: getFieldsMap(2),
                            },
                        },
                    ],
                },
            }
        },
    },
    computed: {
        middlewares() {
            return [
                createInsightsMiddleware({
                    insightsClient: null,
                    onEvent: (event) => {
                        this.$emit('insights-event:' + event.insightsMethod, event)
                        this.$el.dispatchEvent(
                            new CustomEvent('insights-event:' + event.insightsMethod, {
                                bubbles: true,
                                detail: { insightsEvent: event },
                            }),
                        )
                    },
                }),
                ...this.getMiddlewares(),
            ]
        },
    },
}
</script>
