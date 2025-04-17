<script>
import InstantSearchMixin from '../Search/InstantSearchMixin.vue'

export default {
    mixins: [InstantSearchMixin],

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

            config.getQuery = (query, search_attributes) => {
                return [
                    {
                        function_score: {
                            functions: [
                                {
                                    filter: { match: { query_text: query } },
                                    weight: 18
                                },
                                {
                                    filter: { term: { redirect: query } },
                                    weight: 24
                                },
                                {
                                    field_value_factor: {
                                        field: "popularity",
                                        factor: 1,
                                        modifier: "log1p",
                                        missing: 0
                                    },
                                    weight: 5
                                },
                                {
                                    filter: { exists: { field: "redirect" } },
                                    weight: 10
                                },
                                {
                                    filter: { term: { display_in_terms: 1 } },
                                    weight: 2
                                },
                                {
                                    filter: { term: { is_processed: 1 } },
                                    weight: 2
                                }
                            ]
                        },
                    },
                ]
            }

            return config
        },

        async getSearchSettings() {
            return {
                highlight_attributes: ['query_text'],
                search_attributes: [
                    {
                        field: 'query_text',
                        weight: 2.0,
                    },
                    {
                        field: 'redirect',
                        weight: 8.0,
                    },
                ],
                result_attributes: [
                    'query_text',
                    'redirect',
                    'popularity',
                    'num_results',
                    'display_in_terms',
                    'is_processed',
                ],
            }
        },
    },
}
</script>
