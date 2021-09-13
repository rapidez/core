<script>
    import InteractWithUser from './User/mixins/InteractWithUser'

    export default {
        mixins: [InteractWithUser],

        props: {
            query: {
                type: String,
                required: true,
            },
            variables: {
                type: Object,
                default: () => {},
            },
            check: {
                type: String,
            },
            redirect: {
                type: String,
            },
            cache: {
                type: String,
            },
            afterResolvedData: {
                type: Function,
            }
        },

        data: () => ({
            data: null,
            cachePrefix: 'graphql_'
        }),

        render() {
            return this.$scopedSlots.default({
                data: this.data,
                resolveData: this.resolveData,
            })
        },

        created() {
            this.resolveData()
        },

        methods: {
            async resolveData (options = {skip_cache: false})
            {
                if (options.skip_cache || !this.getCache()) {
                    await this.runQuery()
                }

                if (this.afterResolvedData) {
                    this.afterResolvedData(this.data)
                }
            },

            getCache() {
                let cache = false

                if (cache = localStorage[this.cachePrefix + this.cache]) {
                    this.data = JSON.parse(cache)
                }

                return cache
            },

            async runQuery() {
                try {
                    let options = this.$root.user ? { headers: { Authorization: `Bearer ${localStorage.token}` }} : null
                    let response = await axios.post(config.magento_url + '/graphql', {
                        query: this.query,
                        variables: this.variables
                    }, options)

                    if (response.data.errors) {
                        if (response.data.errors[0].extensions.category == 'graphql-authorization') {
                            this.logout('/login')
                        } else {
                            Notify(response.data.errors[0].message, 'error')
                        }
                        return
                    }

                    if (this.check) {
                        if (!eval('response.data.' + this.check)) {
                            Turbolinks.visit(this.redirect)
                            return
                        }
                    }

                    this.data = response.data.data

                    if (this.cache) {
                        localStorage[this.cachePrefix + this.cache] = JSON.stringify(this.data)
                    }
                } catch (e) {
                    Notify(window.config.translations.errors.wrong, 'warning')
                }
            }
        }
    }
</script>
