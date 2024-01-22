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
        },
        limit: {
            type: Number,
            default: 10,
        },
        multiMatchTypes: {
            type: Array,
            default: () => ['best_fields', 'phrase', 'phrase_prefix'],
        },
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    data() {
        return {
            results: {},
            resultsCount: 0,
            searchAdditionals: () => null,
        }
    },

    mounted() {
        this.$nextTick(() => this.$emit('mounted'))
        let self = this

        // Define function here to gain access to the debounce prop
        this.searchAdditionals = useDebounceFn(function (query) {
            if (!self.additionals) {
                return
            }

            // Initialize with empty data to preserve additionals order
            self.results = Object.fromEntries(Object.keys(self.additionals).map(e => [e, []]))
            self.resultsCount = 0

            let url = new URL(config.es_url)
            let auth = `Basic ${btoa(`${url.username}:${url.password}`)}`
            let baseUrl = url.origin

            Object.entries(self.additionals).forEach(([name, data]) => {
                let fields = data['fields'] ?? data
                let limit = data['limit'] ?? self.limit ?? undefined
                let sort = data['sort'] ?? undefined

                let multimatch = self.multiMatchTypes.map((type) => ({
                    multi_match: {
                        query: query,
                        type: type,
                        fields: fields,
                        fuzziness: type.includes('phrase') ? undefined : 'AUTO',
                    },
                }))

                let esQuery = {
                    size: limit,
                    sort: sort,
                    query: {
                        bool: {
                            should: multimatch,
                            minimum_should_match: 1,
                        },
                    },
                }

                axios({
                    url: `${baseUrl}/${config.es_prefix}_${name}_${config.store}/_search`,
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Authorization: auth },
                    data: JSON.stringify(esQuery),
                }).then((response) => {
                    self.results[name] = response.data?.hits ?? []
                    self.resultsCount += self.results[name]?.hits?.length ?? 0
                })
            })
        }, self.debounce)
    },
}
</script>
