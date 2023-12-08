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
    },

    mounted() {
        this.$nextTick(() => this.$emit('mounted'));
    },

    render() {
        return this.$scopedSlots.default(Object.assign(this, { self: this }))
    },

    data() {
        return {
            results: { count: 0 },
        }
    },

    methods: {
        searchAdditionals(query) {
            if (!this.additionals) {
                return
            }

            this.results = { count: 0 }

            let url = new URL(config.es_url)
            let auth = `Basic ${btoa(`${url.username}:${url.password}`)}`
            let baseUrl = url.origin

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
                    url: `${baseUrl}/${config.es_prefix}_${name}_${config.store}/_search`,
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Authorization: auth },
                    data: JSON.stringify(esQuery),
                }).then((response) => {
                    this.results[name] = response.data?.hits ?? []
                    this.results.count += this.results[name]?.hits?.length ?? 0
                })
            })
        },
    },
}
</script>
