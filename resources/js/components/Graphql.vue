<script>
    import InteractWithUser from './User/mixins/InteractWithUser'

    export default {
        mixins: [InteractWithUser],

        props: [
            'query',
            'check',
            'redirect',
            'replace'
        ],

        data: () => ({
            data: null,
            replaceData: null
        }),

        render() {
            return this.$scopedSlots.default({
                data: this.data,
                runQuery: this.runQuery,
                replace: this.replace
            })
        },

        created() {
            this.replaceData = this.replace.includes("localStorage.mask") ? window["localStorage"].mask : this.replace
            this.runQuery()
        },

        methods: {
            async runQuery() {
                try {

                    let options = this.$root.user ? { headers: { Authorization: `Bearer ${localStorage.token}` }} : null
                    let response = await axios.post(config.magento_url + '/graphql', {
                        query: this.query.replace('input', this.replaceData)
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
                } catch (e) {
                    alert('Something went wrong, please try again')
                }
            }
        }
    }
</script>
