<script>
    import InteractWithUser from './User/mixins/InteractWithUser'

    export default {
        mixins: [InteractWithUser],

        props: [
            'query',
            'check',
            'redirect',
            'cache',
        ],

        data: () => ({
            data: null,
        }),

        render() {
            return this.$scopedSlots.default({
                data: this.data,
            })
        },

        created() {
            if (!this.getCache()) {
                this.runQuery()
            }
        },

        methods: {
            getCache() {
                let cache = false

                if (cache = localStorage[this.cache]) {
                    this.data = JSON.parse(cache)
                }

                return cache
            },

            async runQuery() {
                try {
                    let options = this.$root.user ? { headers: { Authorization: `Bearer ${localStorage.token}` }} : null
                    let response = await axios.post(config.magento_url + '/graphql', {
                        query: this.query
                    }, options)

                    if (response.data.errors) {
                        if (response.data.errors[0].extensions.category == 'graphql-authorization') {
                            this.logout('/login')
                        } else {
                            alert(response.data.errors[0].message)
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
                        localStorage[this.cache] = JSON.stringify(this.data)
                    }
                } catch (e) {
                    alert('Something went wrong, please try again')
                }
            }
        }
    }
</script>
