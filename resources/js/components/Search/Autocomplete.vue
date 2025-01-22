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
        size: {
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
            self.results = Object.fromEntries(Object.keys(self.additionals).map((indexName) => [indexName, []]))
            self.resultsCount = 0

            let url = new URL(config.es_url)
            let auth = `Basic ${btoa(`${url.username}:${url.password}`)}`
            let baseUrl = url.origin

            Object.entries(self.additionals).forEach(([name, data]) => {
                let fields = data['fields'] ?? data
                let size = data['size'] ?? self.size ?? undefined
                let sort = data['sort'] ?? undefined
                let fuzziness = data['fuzziness'] ?? 'AUTO'

                let multimatch = self.multiMatchTypes.map((type) => ({
                    multi_match: {
                        query: query,
                        type: type,
                        fields: fields,
                        fuzziness: type.includes('phrase') ? undefined : fuzziness,
                    },
                }))

                let esQuery = {
                    size: size,
                    sort: sort,
                    query: {
                        bool: {
                            should: multimatch,
                            minimum_should_match: 1,
                        },
                    },
                    highlight: {
                        pre_tags: ['<mark>'],
                        post_tags: ['</mark>'],
                        fields: Object.fromEntries(fields.map((field) => [field.split('^')[0], {}])),
                        require_field_match: false,
                    },
                }

                rapidezFetch(`${baseUrl}/${config.es_prefix}_${name}_${config.store}/_search`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Authorization: auth },
                    body: JSON.stringify(esQuery),
                }).then(async (response) => {
                    const responseData = await response.json()

                    self.results[name] = responseData?.hits ?? []
                    self.results.count += self.results[name]?.hits?.length ?? 0
                })
            })
        }, self.debounce)
    },

    methods: {
        highlight(hit, field) {
            let source = hit._source ?? hit.source
            let highlight = hit.highlight ?? source.highlight
            return highlight?.[field]?.[0] ?? source?.[field] ?? ''
        },
    },
}
</script>
