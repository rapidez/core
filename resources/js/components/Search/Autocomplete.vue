<script>
import { ReactiveBase, DataSearch } from '@appbaseio/reactivesearch-vue'
import { useDebounceFn } from '@vueuse/core'

Vue.use(ReactiveBase)
Vue.use(DataSearch)

export default {
    props: {
        additionals: Object,
        debounce: {
            type: Number,
            default: 100,
        }
    },

    render() {
        return this.$scopedSlots.default({
            results: this.results,
            searchAdditionals: this.searchAdditionals,
            additionals: this.additionals,
        })
    },

    data() {
        return {
            results: { count: 0 }
        }
    },

    methods: {
        searchAdditionals(query) {
            if(!this.additionals) {
                return;
            }

            this.results = { count: 0 }

            Object.entries(this.additionals).forEach(([name, fields]) => {
                let esQuery = {
                    query: {
                        multi_match: {
                            query: query,
                            fields: fields,
                            fuzziness: 'AUTO',
                        },
                    },
                }

                axios({
                    url: `${config.es_url}/${config.es_prefix}_${name}_${config.store}/_search`,
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    data: JSON.stringify(esQuery),
                }).then((response) => {
                    this.results[name] = response.data?.hits ?? []
                    this.results.count += this.results[name].hits.length;
                })
            })
        }
    },
}
</script>
