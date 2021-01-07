<script>
export default {
    props: {
        setQuery: Function,
        term: '',
    },
    mounted() {
        this.setQuery({
            query: {
                bool: {
                    should: [
                        {
                            multi_match: {
                                query: this.term,
                                fields: config.searchable,
                                type: 'best_fields',
                                operator: 'or',
                                fuzziness: 'AUTO',
                            },
                        },
                        {
                            multi_match: {
                                query: this.term,
                                fields: config.searchable,
                                type: 'phrase',
                                operator: 'or',
                            }
                        }
                    ]
                }
            }
        });
    },
    render: h => null
};
</script>
