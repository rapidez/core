<script>
import InstantSearchMixin from '../Search/InstantSearchMixin.vue'

export default {
    mixins: [InstantSearchMixin],

    props: {
        forceResults: {
            type: Boolean,
            default: true,
        },
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
        async getInstantSearchClientConfig() {
            const config = await InstantSearchMixin.methods.getInstantSearchClientConfig.bind(this).call()

            config.getQuery = (query, search_attributes, config) => {
                const dsl = [
                    {
                        function_score: {
                            functions: [
                                {
                                    filter: { match: { query_text: query } },
                                    weight: 10,
                                },
                                {
                                    filter: { term: { redirect: query } },
                                    weight: 10,
                                },
                                {
                                    field_value_factor: {
                                        field: 'popularity',
                                        factor: 1,
                                        modifier: 'log1p',
                                        missing: 0,
                                    },
                                    weight: 10,
                                },
                                {
                                    filter: { exists: { field: 'redirect' } },
                                    weight: 1.3,
                                },
                                {
                                    filter: { term: { display_in_terms: 1 } },
                                    weight: 2,
                                },
                                {
                                    filter: { term: { is_processed: 1 } },
                                    weight: 1,
                                },
                            ],
                        },
                    },
                ]

                if (!this.forceResults && query && query !== " ") {
                    dsl.push(this.relevanceQueryMatch(query, search_attributes, config?.fuzziness))
                }

                return dsl
            }

            return config
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

        async getSearchSettings() {
            return {
                highlight_attributes: ['query_text'],
                search_attributes: [
                    {
                        field: 'query_text',
                        weight: 12.0,
                    },
                    {
                        field: 'redirect',
                        weight: 8.0,
                    },
                ],
                result_attributes: ['query_text', 'redirect', 'popularity', 'num_results', 'display_in_terms', 'is_processed'],
                'filter_attributes': [
                    {'attribute': 'query_text', 'field': 'query_text', 'type': 'string'},
                    {'attribute': 'redirect', 'field': 'redirect', 'type': 'string'},
                    {'attribute': 'popularity', 'field': 'popularity', 'type': 'numeric'},
                    {'attribute': 'num_results', 'field': 'num_results', 'type': 'numeric'},
                    {'attribute': 'display_in_terms', 'field': 'display_in_terms', 'type': 'numeric'},
                    {'attribute': 'is_processed', 'field': 'is_processed', 'type': 'numeric'},
                ],
            }
        },
    },
}
</script>
